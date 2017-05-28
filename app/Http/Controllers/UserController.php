<?php

namespace Yap\Http\Controllers;

use Illuminate\Http\Request;
use Yap\Models\User;

class UserController extends Controller
{

    public function index(User $user, Request $request)
    {
        $users = $user->filled()->filter($request->get('filter', null))->sortable(['username'])->paginate(10);

        return view('pages.user.index')->with([
            'users' => $users,
        ]);
    }


    public function profile()
    {
        return $this->show(auth()->user());
    }


    public function show(User $user)
    {
        //Todo: logic if user is current user
        return view('pages.user.show')->withUser($user);
    }


    public function edit()
    {
        return redirect()->away('https://github.com/settings/profile');
    }


    public function update()
    {
    }
}
