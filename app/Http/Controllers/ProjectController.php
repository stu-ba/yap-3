<?php

namespace Yap\Http\Controllers;

use Illuminate\Http\Request;
use Yap\Models\Project;
use Yap\Models\User;

class ProjectController extends Controller
{

    public function removeUser(Request $request, Project $project, User $user)
    {
        //TODO: add policy
        $project->removeMember($user->id);
        $message = 'User \''.$user->username.'\' is scheduled to be removed from project \''.$project->name.'\'.';
        if ($request->isXmlHttpRequest()) {
            return response()->json(['message' => $message], 202);
        }

        alert('warning', $message);

        return redirect()->back();
    }


    public function addUser(Request $request, Project $project, User $user)
    {
        $project->addMember($user->id, $request->get('role', false));

        $message = 'User \''.$user->username.'\' was added to project \''.$project->name.'\' as '.($request->get('role',
                false) ? 'leader' : 'participant').'.';

        if ($request->isXmlHttpRequest()) {
            return response()->json(['message' => $message], 202);
        }

        alert('info', $message);

        return redirect()->back();
    }


    public function makeLeader(Request $request, Project $project, User $user)
    {
    }


    public function makeParticipant(Request $request, Project $project, User $user)
    {
    }
}
