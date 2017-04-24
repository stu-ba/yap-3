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

    public function show(User $user)
    {
        //Todo: logic if user is current userw
        return view('pages.user.show')->withUser($user);
    }


    public function profile()
    {
        return $this->show(auth()->user());
    }

    public function edit() {

    }

    public function update() {

    }
}
