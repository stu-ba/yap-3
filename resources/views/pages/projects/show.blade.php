@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-12">
            <div class="card card-profile">
                <div class="content">
                    <h2 class="title">{{ $project->name }}</h2>
                    <h5 class="category">{!! date_with_hovertip($project->archive_at, 'top', $project->created_at) !!}</h5>
                    <p class="card-content">
                        {{ $project->description }}
                    </p>
                    <p>
                        @includeWhen($current->can('archive', $project), 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Archive', 'class' => 'archive-project btn btn-xs '.((is_null($project->archive_at) || $project->archive_at->gt(\Carbon\Carbon::now())) ?: 'disabled'), 'icon' => fa('archive'), 'customAttributes' => 'data-reload="false" data-project='.$project->id.' data-help='.route('docs', ['page' => 'project#archive'])])
                        @includeWhen($current->can('update', $project), 'components.html.fa-button', ['href' => route('projects.edit', ['project' => $project]), 'tooltip' => 'Edit project', 'class' => 'btn btn-xs ', 'icon' => fa('edit')])
                        @include('components.html.fa-button', ['href' => route('switch.github.repository', ['project' => $project]), 'tooltip' => 'Repository on GitHub', 'class' => 'btn bg-black btn-xs '.(!is_null($project->github_repository_id) ?: 'disabled'), 'icon' => fa('github')])
                        @include('components.html.taiga-button', ['href' => route('switch.taiga.project', ['project' => $project]), 'tooltip' => 'Project on Taiga', 'class' => 'btn btn-grey btn-xs '.(!is_null($project->taiga_id) ?: 'disabled')])
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'projects#users-projects'])
                    {{--<a href="#" class="btn btn-xs btn-white pull-right add-user" data-username="{{ $user->username }}" data-help="{{route('docs', ['page' => 'detail#add-to-project'])}}"><i class="fa {{ fa('add') }}"></i> <span class="hidden-xs">Add user to project</span></a>--}}
                    <h4 class="title">Members of project '{{ $project->name }}'</h4>
                    <p class="category">Users associated to '{{ $project->name }}' project.</p>
                </div>
                <div class="card-content table-responsive">
                    @if ($project->members->isNotEmpty())
                        <table class="table table-hover">
                            <thead class="text-gray">
                            <th>Username</th>
                            <th>Role</th>
                            <th>Added at</th>
                            <th>Actions</th>
                            </thead>
                            <tbody>
                            @foreach ($project->members as $member)
                                <tr class="{{ ($member->pivot->is_leader) ? 'info' : '' }} {{ ($member->pivot->to_be_deleted) ? 'danger' : '' }}">
                                    <td>{{ $member->username }}</td>
                                    <td>{{ ($member->pivot->is_leader) ? 'Team Leader' : 'Participant' }}</td>
                                    <td>{!! date_with_hovertip($member->pivot->created_at) !!}</td>
                                    <td>
                                        @includeWhen($current->can('removeMember', $project) && !$current->is($member), 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Remove user from project', 'class' => 'remove-user-from-project btn btn-xs btn-danger '.(!($member->pivot->to_be_deleted) ?: 'disabled'), 'icon' => fa('remove'), 'customAttributes' => 'data-username='.$member->username.' data-project-id="'.$project->id.'" data-project-name="'.$project->name.'" data-help='.route('docs', ['page' => 'detail#remove-from-project'])])
                                        @include('components.html.fa-button', ['href' => route('users.show', ['user' => $member->username]), 'tooltip' => 'Detail', 'icon' => fa('detail')])
                                        @include('components.html.fa-button', ['href' => route('switch.github.user', ['user' => $member]), 'tooltip' => 'Profile on GitHub', 'class' => 'btn bg-black btn-xs', 'icon' => fa('github')])
                                        @include('components.html.taiga-button', ['href' => route('switch.taiga.user', ['user' => $member]), 'tooltip' => 'Profile on Taiga', 'class' => 'btn btn-xs btn-grey'])
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        @include('partials.no-records')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection