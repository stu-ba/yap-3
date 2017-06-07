<?php

namespace Yap\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yap\Events\TeamRequested;
use Yap\Foundation\Projects\Registrar;
use Yap\Http\Requests\ArchiveProject;
use Yap\Http\Requests\CreateProject;
use Yap\Http\Requests\UpdateProject;
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
        $this->authorize('create', Project::class);

        return view('pages.projects.create')->with([
            'title'        => 'Create project',
            'projectTypes' => $projectType->all(['name', 'id'])->pluck('name', 'id'),
        ]);
    }


    public function store(CreateProject $request, Registrar $projectRegistrar)
    {
        $this->authorize('store', Project::class);

        $members = $request->only(['team_leaders', 'participants']);

        $project = $projectRegistrar->create($request->only(['name', 'description', 'project_type_id', 'archive_at']),
            array_get($members, 'team_leaders'), array_get($members, 'participants', []));

        if ($request->get('create_repository', false)) {
            event(new TeamRequested($project));
            alert('info', 'Creation of repository for project \''.$project->name.'\' was pushed to queue.');
        }

        alert('success', 'Project was created!');

        return redirect()->route('projects.show', ['project' => $project]);
    }


    public function show(Project $project)
    {
        return view('pages.projects.show')->with([
            'title'   => $project->name.'\'s detail',
            'project' => $project->load([
                'members' => function ($query) {
                    $query->orderBy('pivot_is_leader', 'desc')->filled()->orderBy('username');
                },
            ]),
        ]);
    }


    public function edit(Project $project)
    {
        $this->authorize('edit', $project);

        $members = $project->load('members.invitations')->members;

        foreach ($members as $member) {
            if ($member->pivot->is_leader) {
                $leaderEmails[] = $member->email ?? $member->invitations->first()->email;
            } else {
                $participantEmails[] = $member->email ?? $member->invitations->first()->email;
            }
        }

        return view('pages.projects.edit')->with([
            'title'             => 'Edit project '.$project->name,
            'project'           => $project,
            'leaderEmails'      => $leaderEmails ?? [],
            'participantEmails' => $participantEmails ?? [],
        ]);
    }


    public function update(UpdateProject $request, Project $project, Registrar $projectRegistrar)
    {
        $this->authorize('update', $project);

        $members = $request->only(['team_leaders', 'participants']);
        $projectRegistrar->update($request->only(['description', 'archive_at']), $project,
            array_get($members, 'team_leaders'), array_get($members, 'participants'));

        if ($request->get('create_repository', false)) {
            event(new TeamRequested($project));
            alert('info', 'Creation of repository for project \''.$project->name.'\' was pushed to queue.');
        }

        alert('success', 'Project was updated!');

        return redirect()->route('projects.show', ['project' => $project]);
    }


    public function archive(ArchiveProject $request, Project $project)
    {
        //$dateInstance = ->endOfDay();
        $this->authorize('archive', Project::class);

        $project->update(['archive_at' => Carbon::createFromTimestamp($request->get('archive_at', time()))]);

        $message =
            'Project \''.$project->name.'\' is scheduled to be archived at \''.$project->archive_at->diffForHumans().'\'.';
        if ($request->isXmlHttpRequest()) {
            return response()->json(['message' => $message], 202);
        }

        alert('note', $message);

        return redirect()->back();
    }


    public function removeUser(Request $request, Project $project, User $user)
    {
        $this->authorize('removeMember', $project);

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
        $this->authorize('addMember', $project);

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
