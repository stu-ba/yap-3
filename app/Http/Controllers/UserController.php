<?php

namespace Yap\Http\Controllers;

use Illuminate\Http\Request;
use Yap\Http\Requests\BanUser;
use Yap\Models\User;

class UserController extends Controller
{

    public function index(User $user, Request $request)
    {
        //TODO: add policy for filter
        $users = $user->filled()->filter($request->get('filter', null))->sortable(['username'])->paginate(10);

        return view('pages.user.index')->with([
            'title' => 'User listing',
            'users' => $users,
        ]);
    }


    public function notifications(Request $request)
    {
        $notifications = $request->user()->notifications()->filter($request->get('filter', null))->paginate(20);

        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        return view('pages.user.notifications')->with([
            'title'         => 'Notifications',
            'notifications' => $notifications,
        ]);
    }


    public function profile()
    {
        return view('pages.user.show')->withTitle('Your profile')->withUser(auth()->user());
    }


    public function show(User $user)
    {
        if ($user == auth()->user()) {
            return redirect()->route('profile');
        }

        return view('pages.user.show')->withTitle($user->username.'\'s profile')->withUser($user);
    }


    public function edit()
    {
        return redirect()->away('https://github.com/settings/profile');
    }


    public function ban(BanUser $request, User $user)
    {
        //TODO: add policy
        $user->ban($request->get('reason'));

        if ($request->isXmlHttpRequest()) {
            return response()->json(['success' => true], 200);
        }

        alert('warning', 'User \''.$user->username.'\' was banned.');

        return redirect()->route('users.show', ['user' => $user]);
    }


    public function unban(Request $request, User $user)
    {
        //TODO: add policy
        $user->unban();

        if ($request->isXmlHttpRequest()) {
            return response()->json(['success' => true], 200);
        }

        alert('success', 'User \''.$user->username.'\' ban was removed.');

        return redirect()->route('users.show', ['user' => $user]);
    }


    public function promote(Request $request, User $user)
    {
        //TODO: add policy
        $user->promote();

        if ($request->isXmlHttpRequest()) {
            return response()->json(['success' => true], 200);
        }
        alert('success', 'User \''.$user->username.'\' was promoted to administrator.');

        return redirect()->route('users.show', ['user' => $user]);
    }


    public function demote(Request $request, User $user)
    {
        //TODO: add policy
        $user->demote();

        if ($request->isXmlHttpRequest()) {
            return response()->json(['success' => true], 200);
        }
        alert('warning', 'User \''.$user->username.'\' was removed from administrators.');

        return redirect()->route('users.show', ['user' => $user]);
    }


    public function availableProjects(Request $request, User $user)
    {
        //TODO: add policy

        if ($request->isXmlHttpRequest()) {
            return response()->json($user->unassociatedProjects()->pluck('name', 'id'), 200);
        }

        alert('info', 'Sorry, previous request is available only for XmlRequests.');

        return redirect()->route('users.show', ['user' => $user]);
    }


    public function projectList(Request $request, User $user)
    {

        if ($request->isXmlHttpRequest()) {
            return response()->json($user->projects()->wherePivot('to_be_deleted', '=', false)->pluck('name', 'id'), 200);
        }

        alert('info', 'Sorry, previous request is available only for XmlRequests.');

        return redirect()->route('users.show', ['user' => $user]);
    }
}
