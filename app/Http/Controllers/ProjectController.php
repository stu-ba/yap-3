<?php

namespace Yap\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yap\Http\Requests\ArchiveProject;
use Yap\Http\Requests\CreateProject;
use Yap\Models\Project;
use Yap\Models\ProjectType;
use Yap\Models\User;

class ProjectController extends Controller
{

    /**
     * @param \Yap\Models\Project      $project
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function index(Project $project, Request $request)
    {
        //TODO: add policy for filter
        $projects = $project->filter($request->get('filter', null))->sortable(['name'])->paginate(10);

        return view('pages.projects.index')->with([
            'title'    => 'Projects listing',
            'projects' => $projects,
        ]);
    }


    public function create(ProjectType $projectType)
    {
        return view('pages.projects.create')->with([
            'title' => 'Create project',
            'projectTypes' => $projectType->all(['name', 'id'])->pluck('name', 'id'),
        ]);
    }


    public function store(CreateProject $request)
    {
        //TODO: take care of parsing options
    }


    public function show(Project $project)
    {
        return view('pages.projects.show')->with([
            'title'   => $project->name.'\'s detail',
            'project' => $project->load([
                'members' => function ($query) {
                    $query->orderBy('pivot_is_leader', 'desc')->orderBy('username');
                },
            ]),
        ]);
    }


    public function archive(ArchiveProject $request, Project $project)
    {
        //TODO: add policy

        $dateInstance = Carbon::createFromTimestamp($request->get('archive_at', time()))->endOfDay();

        $project->update(['archive_at' => $dateInstance]);

        $message =
            'Project \''.$project->name.'\' is scheduled to be archived at \''.$dateInstance->diffForHumans().'\'.';
        if ($request->isXmlHttpRequest()) {
            return response()->json(['message' => $message], 202);
        }

        alert('note', $message);

        return redirect()->back();
    }


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
