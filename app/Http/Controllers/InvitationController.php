<?php

namespace Yap\Http\Controllers;

use Yap\Exceptions\InvitationRegistrarException;
use Yap\Foundation\InvitationRegistrar;
use Yap\Http\Requests\StoreInvitation;
use Yap\Models\Invitation;

class InvitationController extends Controller
{

    public function create(string $email = null, Invitation $invitation)
    {
        if ( ! is_null($email) && ! is_email($email)) {
            $email = null;
        }

        $invitations = $invitation->with('inviter')->validUntil()->active()->sortable()->paginate(10);

        return view('pages.invitation.create')->withTitle('Create Invitation')->withEmail($email)
                                              ->withInvitations($invitations);
    }


    public function store(StoreInvitation $request, InvitationRegistrar $registrar)
    {
        try {
            /** @var Invitation $invitation */
            $invitation = $registrar->invite($request->get('email'), $request->only([
                'admin',
                'force_resend',
                'indefinite',
                'dont_send',
            ]));

            if ($request->isXmlHttpRequest()) {
                return response()->json(['success' => true], 200);
            }

            $this->flashAlert($invitation);
        } catch (InvitationRegistrarException $exception) {
            $this->handleException($exception);
        }

        return redirect()->route('invitations.create');
    }


    protected function flashAlert(Invitation $invitation): void
    {
        if ( ! is_null($invitation->depleted_at)) {
            alert('info',
                'User \''.($invitation->user->name ?? $invitation->user->username).'\' was granted access and can freely login to '.config('yap.short_name').'.');
        } elseif ($invitation->wasRecentlyCreated) {
            alert('success', 'Invitation for potential user with email \''.$invitation->email.'\' was created.');
        } elseif (is_null($invitation->valid_until)) {
            alert('info', 'Invitation for potential user with email \''.$invitation->email.'\' is now valid forever.');
        } else {
            alert('info', 'Invitation for potential user with email \''.$invitation->email.'\' was prolonged.');
        }
    }


    /**
     * @param $exception
     */
    protected function handleException($exception): void
    {
        if ($exception->getCode() === 0) {
            alert('error', $exception->getMessage());
        } else {
            alert('info', $exception->getMessage());
        }
    }
}
