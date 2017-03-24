<?php

namespace Yap\Foundation;

use Carbon\Carbon;
use Yap\Exceptions\InvitationRegistrarException;
use Yap\Models\Invitation;
use Yap\Models\User;

class InvitationRegistrar
{

    /**
     * @var User
     */
    private $user;

    /**
     * @var Invitation
     */
    private $invitation;

    /**
     * @var string
     */
    private $email;

    /**
     * @var User
     */
    private $creator;

    /**
     * @var array
     */
    private $options = [
        'admin'        => false,
        'force_resend' => false,
        'indefinite'   => false,
        'dont_send'    => false
    ];


    /**
     * InvitationRegistrar constructor.
     *
     * @param User       $user
     * @param Invitation $invitation
     */
    public function __construct(User $user, Invitation $invitation)
    {
        $this->user = $user;
        $this->invitation = $invitation;
        $this->creator = auth()->id() ?? systemAccount();
    }


    public function invite(string $email, array $options = [])
    {
        $this->reset();
        $this->setOptions($options)->setEmail($email);
        /** @var User $user */
        $user = $this->user->whereEmail($email)->first();
        /** @var Invitation $invitation */
        $invitation = $this->invitation->whereEmail($email)->first();

        if (is_null($invitation) && is_null($user)) {
            //no email in system

            $this->make($email);
            return;
        } elseif ( ! is_null($invitation) && ! is_null($user)) {
            if ($user->is_banned) {
                $this->throwBanned();
            } elseif ($user->is_confirmed && ! $user->is_banned) {
                // do nothing, inform user is already registered maybe
                throw new InvitationRegistrarException('User specified by email \''.$email.'\' is already confirmed.',
                    1);
            } elseif ( ! $user->is_confirmed && ! $user->is_banned) {
                //this should never happen simply because user is not updated until registration happens
                //TODO: do something nicer here
                abort(500, 'Something went terribly wrong, consult an administrator.');
            }
        } elseif ( ! is_null($invitation) && is_null($user)) {
            //invitation exists for this email
            //user does not exist for this email
            if (is_null($invitation->valid_until)) {
                //send email urge to register
                return $this;
            } elseif ($invitation->valid_until->lessThan(Carbon::now())) {
                //send email prolonged...
            }

            $invitation->prolong();
            //if valid until is done prolong it one week and send email
            //if valid until is not done prolong and do nothing
        } elseif (is_null($invitation) && ! is_null($user)) {
            if ($user->is_banned) {
                $this->throwBanned();
            }

            if ($this->options['admin'] && $this->creator->is_admin) {
                $user->makeAdmin();
            }

            $invitation = $this->invitation->fill([
                'email'       => $this->email,
                'is_depleted' => true,
                'depleted_at' => Carbon::now(),
                'valid_until' => Carbon::now(),
            ]);

            $user->confirm()->invitation()->save($invitation);
            //update to admin if set to send notification
            //confirm user and send email that user was granted access to yap,
            //create invitation for the email and dont send it
        }

    }


    private function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }


    private function make(): self
    {
        $this->invitation->fill([
            'email'       => $this->email,
            'valid_until' => $this->options['indefinite'] ? 0 : null
        ]);

        $this->user->fill(['is_admin' => $this->options['admin']])->save();
        $this->user->invitation()->save($this->invitation);
        return $this;
    }


    /**
     * Throw banned exception.
     * @throws InvitationRegistrarException
     */
    private function throwBanned(): void
    {
        throw new InvitationRegistrarException('User specified by email \''.$this->email.'\' is banned.', 0);
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

    private function resetOptions(): void
    {
        array_fill_keys(array_keys($this->options), false);
    }


    private function reset()
    {
        $this->resetOptions();
        $this->invitation = $this->invitation->newInstance();
        $this->user = $this->user->newInstance();
    }

}