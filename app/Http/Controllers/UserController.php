<?php

namespace Yap\Http\Controllers;

use Illuminate\Http\Request;
use Yap\Models\User;

class UserController extends Controller
{

    public function index(User $user, Request $request)
    {
        $users = $user->filled()->filter($request->get('filter', 'all'))->sortable()->paginate(10);

        return view('pages.user.index')->with([
            'users' => $users,
            'title' => 'test',
        ]);
    }

    public function show(User $user)
    {
        d($user);
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
