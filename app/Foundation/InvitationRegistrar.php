<?php

namespace Yap\Foundation;

use Carbon\Carbon;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Mailable;
use Yap\Exceptions\InvitationRegistrarException;
use Yap\Mail\InvitationProlonged;
use Yap\Mail\InvitationUrged;
use Yap\Mail\UserAccessGranted;
use Yap\Mail\UserInvited;
use Yap\Models\Invitation;
use Yap\Models\User;

class InvitationRegistrar
{

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Invitation
     */
    protected $invitation;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var User
     */
    protected $inviter;

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var array
     */
    private $options = [
        'admin'        => false,
        'force_resend' => false,
        'indefinite'   => false,
        'dont_send'    => false,
    ];


    /**
     * InvitationRegistrar constructor.
     *
     * @param User       $user
     * @param Invitation $invitation
     * @param Mailer     $mailer
     */
    public function __construct(User $user, Invitation $invitation, Mailer $mailer)
    {
        $this->user = $user;
        $this->invitation = $invitation;
        $this->mailer = $mailer;
    }


    public function invite(string $email, array $options = []): Invitation
    {
        /** @var User $user */
        /** @var Invitation $invitation */
        list($user, $invitation) = $this->setUp($email, $options);

        if (is_null($invitation) && is_null($user)) {
            $this->makeBare()->mail(new UserInvited($this->user->invitations->first()));

            return $this->user->invitations->first();
        } elseif ( ! is_null($invitation) && ! is_null($user)) {
            // This always throws exception catch it!
            $this->invitationAndUserFound($user);
        } elseif ( ! is_null($invitation) && is_null($user)) {
            $invitation = $this->processOptions($invitation);

            if (is_null($invitation->valid_until)) {
                $this->mail(new InvitationUrged($invitation));

                return $invitation;
            } elseif ($invitation->valid_until->lessThan(Carbon::now()) || $this->options['force_resend']) {
                //send email if valid until is in past...
                $this->mail(new InvitationProlonged($invitation));
            }
            $invitation->prolong();

            return $invitation;
        } elseif (is_null($invitation) && ! is_null($user)) {
            $this->updateAdmin($user);

            $invitation = $this->invitation->fill([
                'email'       => $this->email,
                'is_depleted' => true,
                'depleted_at' => Carbon::now(),
                'valid_until' => Carbon::now(),
            ]);

            $user->confirm()->invitations()->save($invitation);
            $this->mail(new UserAccessGranted($user));

            return $invitation;
        }
    }


    /**
     * @param string $email
     * @param array  $options
     *
     * @return array
     */
    private function setUp(string $email, array $options): array
    {
        $this->reset();
        $this->setInviter()->setOptions($options)->setEmail($email);

        /** @var User $user */
        $user = $this->user->with('invitations')->whereEmail($email)->first();
        /** @var Invitation $invitation */
        $invitation = $this->invitation->whereEmail($email)->first();

        list($invitation, $user) = $this->checkRelations($user, $invitation);
        $this->handleBannedUser($user);

        return [$user, $invitation];
    }


    /**
     * Re-instantiate user and invitation. Reset options to defaults.
     */
    private function reset()
    {
        array_fill_keys(array_keys($this->options), false);
        $this->invitation = $this->invitation->newInstance();
        $this->user = $this->user->newInstance();
    }


    /**
     * @param array $options
     *
     * @return InvitationRegistrar
     */
    private function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }


    /**
     * Sets inviter to either currently signed in user or systemAccount();.
     */
    private function setInviter(): self
    {
        if (is_null($this->inviter)) {
            $this->inviter = auth()->id() ?? systemAccount();
        }

        return $this;
    }


    /**
     * @param User $user
     * @param Invitation $invitation
     *
     * @return array
     */
    private function checkRelations($user, $invitation): array
    {
        if ( ! is_null($user) && $user->invitations->isNotEmpty()) {
            // This can be any invitation from collection so lets grab first
            $invitation = $user->invitations->first();
        } elseif ( ! is_null($invitation) && ! is_null($invitation->user->email)) {
            $user = $invitation->user;
        }

        return [$invitation, $user];
    }


    /**
     * Throw exception if provided user is banned.
     *
     * @param $user
     *
     * @throws InvitationRegistrarException
     */
    private function handleBannedUser($user)
    {
        if ( ! is_null($user) && $user->is_banned) {
            throw new InvitationRegistrarException('User specified by email \''.$this->email.'\' is banned.', 0);
        }
    }


    /**
     * Send mail if dont_send option is not set to true.
     *
     * @param Mailable $mailable
     *
     * @return bool
     */
    private function mail(Mailable $mailable): bool
    {
        if (property_exists($mailable, 'invitation') && is_null($mailable->invitation->email)) {
            throw new \LogicException('Invitation\'s email can not be null.');
        } elseif (property_exists($mailable, 'user') && is_null($mailable->user->email)) {
            throw new \LogicException('User\'s email can not be null.');
        }

        if ( ! $this->options['dont_send']) {

            $this->mailer->send($mailable);

            return true;
        }

        return false;
    }


    /**
     * Make bare invitation.
     *
     * On creation invitation valid until behaves differently than expected,
     * see boot method on invitation model.
     *
     * @return InvitationRegistrar
     */
    private function makeBare(): self
    {
        $this->invitation->fill([
            'email'       => $this->email,
            'valid_until' => $this->options['indefinite'] ? 0 : null,
        ]);

        $this->user->fill(['is_admin' => $this->options['admin']])->save();
        $this->user->invitations()->save($this->invitation);

        return $this;
    }


    /**
     * Logic when invitation and user have same email.
     *
     * @param $user
     *
     * @throws InvitationRegistrarException
     */
    private function invitationAndUserFound($user): void
    {
        if ($user->is_confirmed) {
            throw new InvitationRegistrarException('User specified by email \''.$this->email.'\' is already confirmed.',
                1);
        }
        //TODO: this should never happen
        throw new InvitationRegistrarException('User is not confirmed but invitation is depleted. Someone has fiddled with database.',
            2);
    }


    private function processOptions(Invitation $invitation): Invitation
    {
        $this->updateAdmin($invitation->user);

        if ($this->options['indefinite']) {
            $invitation->makeIndefinite();
        }

        return $invitation->updateInviter($this->inviter);
    }


    /**
     * @param User $user
     */
    private function updateAdmin(User $user): void
    {
        if ($this->inviter->is_admin) {
            $this->options['admin'] ? $user->promote() : $user->demote();
        }
    }


    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * @param string $email
     *
     * @return InvitationRegistrar
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
