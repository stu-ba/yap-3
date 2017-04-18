<?php

namespace Yap\Http\Controllers;

use Prologue\Alerts\Facades\Alert;
use Yap\Exceptions\InvitationRegistrarException;
use Yap\Foundation\InvitationRegistrar;
use Yap\Http\Requests\StoreInvitation;
use Yap\Models\Invitation;

class InvitationController extends Controller
{

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
                return response()->json(['Success'], 201);
            }

            $this->flashAlert($invitation);
        } catch (InvitationRegistrarException $exception) {
            $this->handleException($exception);
        }
        finally {
            return redirect()->route('invitations.create');
        }
    }


    /**
     * @param $invitation
     */
    protected function flashAlert($invitation): void
    {
        if ($invitation->is_depleted) {
            Alert::info('User \''.($invitation->user->name ?? $invitation->user->username).'\' was granted access and can freely login to '.config('yap.short_name').'.')->flash();
        } elseif ($invitation->created_at->diffInSeconds() <= 20) {
            Alert::success('Invitation for potential user with email \''.$invitation->email.'\' was created.')->flash();
        } elseif (is_null($invitation->valid_until)) {
            Alert::info('Invitation for potential user with email \''.$invitation->email.'\' is now valid forever.')->flash();
        } else {
            Alert::info('Invitation for potential user with email \''.$invitation->email.'\' was prolonged.')->flash();
        }
    }


    /**
     * @param $exception
     */
    protected function handleException($exception): void
    {
        if ($exception->getCode() === 0) {
            Alert::error($exception->getMessage())->flash();
        } else {
            Alert::info($exception->getMessage())->flash();
        }
    }


    public function create(string $email = null, Invitation $invitation)
    {
        if ( ! is_null($email) && ! is_email($email)) {
            $email = null;
        }

        $invitations = $invitation->with('inviter')->recent(8)->active()->get();

        return view('pages.invitation.create')->withEmail($email)->withInvitations($invitations);
    }
}
