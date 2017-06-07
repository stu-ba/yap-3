<?php

namespace Yap\Http\Controllers;

use Illuminate\Http\Request;
use Yap\Http\Requests\ManageUser;
use Yap\Models\User;

class UserController extends Controller
{

    public function index(User $user, Request $request)
    {
        if ($request->get('filter') === 'admin' || $request->get('filter') === 'banned') {
            $this->authorize('filter', User::class);
        }

        $users = $user->filled()->filter($request->get('filter', null))->sortable(['username'])->paginate(10);

        return view('pages.user.index')->with([
            'title' => 'Users listing',
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
        return $this->renderDetail('Your profile', auth()->user());
    }


    /**
     * Render detail page.
     *
     * @param string           $title
     * @param \Yap\Models\User $user
     *
     * @return $this
     */
    protected function renderDetail(string $title, User $user)
    {
        return view('pages.user.show')->with([
            'title' => $title,
            'user'  => $user->load([
                'projects' => function ($query) {
                    $query->orderBy('pivot_to_be_deleted')->orderBy('archive_at', 'desc');
                },
            ]),
        ]);
    }


    public function show(User $user)
    {
        if ($user->is(auth()->user())) {
            return redirect()->route('profile');
        }

        return $this->renderDetail($user->username.'\'s profile', $user);
    }


    public function edit()
    {
        $this->middleware('github:throw');

        return redirect()->away('https://github.com/settings/profile');
    }


    public function ban(ManageUser $request, User $user)
    {
        $this->authorize('manage', $user);
        $user->ban($request->get('reason'));

        if ($request->isXmlHttpRequest()) {
            return response()->json(['success' => true], 200);
        }

        alert('warning', 'User \''.$user->username.'\' was banned.');

        return redirect()->route('users.show', ['user' => $user]);
    }


    public function unban(ManageUser $request, User $user)
    {
        $this->authorize('manage', $user);
        $user->unban();

        if ($request->isXmlHttpRequest()) {
            return response()->json(['success' => true], 200);
        }

        alert('success', 'User \''.$user->username.'\' ban was removed.');

        return redirect()->route('users.show', ['user' => $user]);
    }


    public function promote(ManageUser $request, User $user)
    {
        $this->authorize('manage', $user);
        $user->promote();

        if ($request->isXmlHttpRequest()) {
            return response()->json(['success' => true], 200);
        }
        alert('success', 'User \''.$user->username.'\' was promoted to administrator.');

        return redirect()->route('users.show', ['user' => $user]);
    }


    public function demote(ManageUser $request, User $user)
    {
        $this->authorize('manage', $user);
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
            return response()->json($request->user()->assignableProjectsFor($user)->where('is_archived', '!=', true)
                                            ->pluck('name', 'id'), 200);
        }

        alert('info', 'Sorry, previous request is available only for XmlRequests.');

        return redirect()->route('users.show', ['user' => $user]);
    }


    public function projectList(Request $request, User $user)
    {

        if ($request->isXmlHttpRequest()) {
            return response()->json($user->projects()->wherePivot('to_be_deleted', '=', false)->pluck('name', 'id'),
                200);
        }

        alert('info', 'Sorry, previous request is available only for XmlRequests.');

        return redirect()->route('users.show', ['user' => $user]);
    }
}
