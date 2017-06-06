@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'projects#listing'])
                    @can('create', Yap\Models\Project::class)
                        <a href="{{ route('projects.create') }}" class="btn btn-xs btn-white pull-right"><i class="fa {{ fa('new') }}"></i> <span class="hidden-xs">Create</span></a>
                    @endif
                    <h4 class="title">Project listing</h4>
                    <p class="category">Projects may be filtered and sorted.</p>
                </div>
                <div class="card-filter">
                    <span class="filter-title hidden-xs">Filter by</span>
                    <ul class="nav nav-tabs">
                        <li class="{{ set_active_filter('all', ['mine', 'archived']) ?? 'active' }}">
                            <a href="{{ route('projects.index', ['filter' => 'all']) }}">
                                <i class="fa fa-lg {{ fa('all') }}"></i><span class="hidden-xs">all</span>
                                <div class="ripple-container"></div>
                            </a>
                        </li>
                        <li class="{{ set_active_filter('mine') }}">
                            <a href="{{ route('projects.index', ['filter' => 'mine']) }}">
                                <i class="fa fa-lg {{ fa('mine') }}"></i><span class="hidden-xs">mine</span>
                                <div class="ripple-container"></div>
                            </a>
                        </li>
                        <li class="{{ set_active_filter('archived') }}">
                            <a href="{{ route('projects.index', ['filter' => 'archived']) }}">
                                <i class="fa fa-lg {{ fa('archive') }}"></i><span class="hidden-xs">archived</span>
                                <div class="ripple-container"></div>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-content table-responsive">
                    @if ($projects->isNotEmpty())
                        <table class="table table-hover">
                            <thead class="text-gray">
                                <th>@sortablelink('name', 'name')</th>
                                <th>Team Leaders</th>
                                <th>Participants</th>
                                <th>@sortablelink('created_at', 'created at')</th>
                                <th>@sortablelink('archive_at', 'archive at')</th>
                                <th class="col-xs-3">Actions</th>
                            </thead>
                            <tbody>
                                @foreach ($projects as $project)
                                    <tr class="{{ (!is_null($project->archive_at) && $project->archive_at->isToday()) ? 'info': '' }}">
                                        <td>{!! text_with_hovertip($project->name, $project->description, 'top', 20) !!}</td>
                                        <td>@include('components.html.members', ['project' => $project, 'type' => 'leaders'])</td>
                                        <td>@include('components.html.members', ['project' => $project, 'type' => 'participants'])</td>
                                        <td>{!! date_with_hovertip($project->created_at) !!}</td>
                                        <td>{!! date_with_hovertip($project->archive_at, 'top', $project->created_at) !!}</td>
                                        <td>
                                            @includeWhen($current->can('archive', $project), 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Archive', 'class' => 'archive-project btn btn-xs'.disabledIf(is_null($project->archive_at) || $project->archive_at->gt(\Carbon\Carbon::now())), 'icon' => fa('archive'), 'customAttributes' => 'data-project='.$project->id.' data-help='.route('docs', ['page' => 'project#archive'])])
                                            @includeWhen($current->can('update', $project), 'components.html.fa-button', ['href' => route('projects.edit', ['project' => $project]), 'tooltip' => 'Edit project', 'class' => 'btn btn-xs btn-primary'.disabledIf(!$project->is_archived), 'icon' => fa('edit')])
                                            @include('components.html.fa-button', ['href' => route('projects.show', ['project' => $project]), 'tooltip' => 'Project detail', 'class' => 'btn btn-xs', 'icon' => fa('detail')])
                                            @include('components.html.fa-button', ['href' => route('switch.github.repository', ['project' => $project]), 'tooltip' => 'Repository on GitHub', 'class' => 'btn bg-black btn-xs '.disabledIf(!is_null($project->github_repository_id)), 'icon' => fa('github')])
                                            @include('components.html.taiga-button', ['href' => route('switch.taiga.project', ['project' => $project]), 'tooltip' => 'Project on Taiga', 'class' => 'btn btn-grey btn-xs '.disabledIf(!is_null($project->taiga_id))])
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-centered">
                            {!! $projects->appends(\Request::except('page'))->render() !!}
                        </div>
                    @else
                        @include('partials.no-records')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
