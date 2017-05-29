@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-md-4">
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
                    @if($user->isBanned())
                        @include('components.html.fa-button', ['href' => '#', 'tooltip' => 'Unban user', 'class' => 'unban-user btn btn-success btn-sm', 'icon' => fa('ban'), 'customAttributes' => 'data-username='.$user->username.''])
                    @else
                        @include('components.html.fa-button', ['href' => '#', 'tooltip' => 'Ban user', 'class' => 'ban-user btn btn-danger btn-sm', 'icon' => fa('ban'), 'customAttributes' => 'data-username='.$user->username.''])
                        @include('components.html.fa-button', ['href' => 'https://github.com/'.$user->username, 'tooltip' => 'Profile on GitHub', 'class' => 'btn bg-black btn-sm external', 'icon' => fa('github')])
                        @include('components.html.taiga-button', ['href' => route('switch.user', ['user' => $user]), 'tooltip' => 'Profile on Taiga', 'class' => 'btn btn-sm btn-grey'])
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'projects#my-projects'])
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
                                <tr>
                                    <td>{!! text_with_hovertip($project->name, $project->description) !!}</td>
                                    <td>{{ ($project->pivot['is_leader']) ? 'Team Leader' : 'Participant' }}</td>
                                    <td>{!! date_with_hovertip($project->created_at) !!}</td>
                                    <td>{!! date_with_hovertip($project->archive_at, 'top', $project->created_at) !!}</td>
                                    <td>
                                        @include('components.html.fa-button', ['href' => route('switch.repository', ['project' => $project]), 'tooltip' => 'Repository on GitHub', 'class' => 'btn bg-black btn-xs '.(!is_null($project->github_repository_id) ?: 'disabled'), 'icon' => fa('github')])
                                        @include('components.html.taiga-button', ['href' => route('switch.project', ['project' => $project]), 'tooltip' => 'Project on Taiga', 'class' => 'btn btn-grey btn-xs '.(!is_null($project->taiga_id) ?: 'disabled')])
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