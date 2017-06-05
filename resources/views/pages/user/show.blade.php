@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-12">
            <div class="card card-profile">
                <div class="card-avatar">
                    <a href="#">
                        <img class="img" src="{{ avatar($user) }}">
                    </a>
                </div>
                <div class="content">
                    <h5 class="category">{{ $user->name ?? config('yap.placeholders.name') }}</h5>
                    <h4 class="card-title">{{ $user->username }}</h4>
                    <p class="card-content">
                        {{ $user->bio ?? config('yap.placeholders.bio') }}
                    </p>
                    <p>
                    @if($user->isBanned())
                        @includeWhen($current->can('manage', $user), 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Unban user', 'class' => 'unban-user btn btn-success btn-xs', 'icon' => fa('ban'), 'customAttributes' => 'data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#remove-ban'])])
                    @else
                        @includeWhen($current->can('manage', $user) && !$user->is_admin, 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Promote user to administrator', 'class' => 'promote-user btn btn-default btn-xs', 'icon' => fa('promote'), 'customAttributes' => 'data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#promote'])])
                        @includeWhen($current->can('manage', $user) && $user->is_admin, 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Remove user from administrators', 'class' => 'demote-user btn btn-warning btn-xs', 'icon' => fa('demote'), 'customAttributes' => 'data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#demote'])])
                        @includeWhen($current->can('manage', $user), 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Ban user', 'class' => 'ban-user btn btn-danger btn-xs', 'icon' => fa('ban'), 'customAttributes' => 'data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#ban'])])
                        @include('components.html.fa-button', ['href' => route('switch.github.user', ['user' => $user]), 'tooltip' => 'Profile on GitHub', 'class' => 'btn bg-black btn-xs external', 'icon' => fa('github')])
                        @include('components.html.taiga-button', ['href' => route('switch.taiga.user', ['user' => $user]), 'tooltip' => 'Profile on Taiga', 'class' => 'btn btn-xs btn-grey'])
                    @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'projects#users-projects'])
                    @can('assignProjects', \Yap\Models\User::class)
                         {{--TODO: add policy--}}
                        <a href="#" class="btn btn-xs btn-white pull-right add-user" data-username="{{ $user->username }}" data-help="{{route('docs', ['page' => 'detail#add-to-project'])}}"><i class="fa {{ fa('add') }}"></i> <span class="hidden-xs">Add user to project</span></a>
                    @endcan
                    <h4 class="title">{{ $user->username }}'s projects</h4>
                    <p class="category">Projects associated to {{ $user->name ?? $user->username }}.</p>
                </div>
                <div class="card-content table-responsive">
                    @if ($user->projects->isNotEmpty())
                        <table class="table table-hover">
                            <thead class="text-gray">
                            <th>Name</th>
                            <th>Role</th>
                            <th>Created at</th>
                            <th>Archive/d at</th>
                            <th>Actions</th>
                            </thead>
                            <tbody>
                            @foreach ($user->projects as $project)
                                <tr class="{{ ($project->pivot->to_be_deleted) ? 'danger' : '' }}">
                                    <td>{!! text_with_hovertip($project->name, $project->description) !!}</td>
                                    <td>{{ ($project->pivot['is_leader']) ? 'Team Leader' : 'Participant' }}</td>
                                    <td>{!! date_with_hovertip($project->created_at) !!}</td>
                                    <td>{!! date_with_hovertip($project->archive_at, 'top', $project->created_at) !!}</td>
                                    <td>
                                        @include('components.html.fa-button', ['href' => route('projects.show', ['project' => $project]), 'tooltip' => 'Project detail', 'class' => 'btn btn-xs', 'icon' => fa('detail')])
                                        @includeWhen($current->is_admin, 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Remove user from project', 'class' => 'remove-user-from-project btn btn-xs btn-danger '.(!($project->pivot->to_be_deleted) ?: 'disabled'), 'icon' => fa('remove'), 'customAttributes' => 'data-username='.$user->username.' data-project-id="'.$project->id.'" data-project-name="'.$project->name.'" data-help='.route('docs', ['page' => 'detail#remove-from-project'])])
                                        @include('components.html.fa-button', ['href' => route('switch.github.repository', ['project' => $project]), 'tooltip' => 'Repository on GitHub', 'class' => 'btn bg-black btn-xs '.(!is_null($project->github_repository_id) ?: 'disabled'), 'icon' => fa('github')])
                                        @include('components.html.taiga-button', ['href' => route('switch.taiga.project', ['project' => $project]), 'tooltip' => 'Project on Taiga', 'class' => 'btn btn-grey btn-xs '.(!is_null($project->taiga_id) ?: 'disabled')])
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