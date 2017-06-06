@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'users#listing'])
                    @can('create', \Yap\Models\Invitation::class)
                        <a href="{{ route('invitations.create') }}" class="btn btn-xs btn-white pull-right invite-user"><i
                                    class="fa {{ fa('invite') }}"></i> <span class="hidden-xs">Invite</span></a>
                    @endcan
                    <h4 class="title">User listing</h4>
                    <p class="category">Users may be filtered and sorted.</p>
                </div>
                <div class="card-filter">
                    <span class="filter-title hidden-xs">Filter by</span>
                    <ul class="nav nav-tabs">
                        <li class="{{ set_active_filter('all', ['colleagues', 'banned', 'admins']) ?? 'active' }}">
                            <a href="{{ route('users.index', ['filter' => 'all']) }}">
                                <i class="fa fa-lg {{ fa('all') }}"></i><span class="hidden-xs">all</span>
                                <div class="ripple-container"></div>
                            </a>
                        </li>
                        <li class="{{ set_active_filter('colleagues') }}">
                            <a href="{{ route('users.index', ['filter' => 'colleagues']) }}">
                                <i class="fa fa-lg {{ fa('colleagues') }}"></i><span class="hidden-xs">colleagues</span>
                                <div class="ripple-container"></div>
                            </a>
                        </li>
                        @can('filter', \Yap\Models\User::class)
                            <li class="{{ set_active_filter('banned') }}">
                                <a href="{{ route('users.index', ['filter' => 'banned']) }}">
                                    <i class="fa fa-lg {{ fa('banned') }}"></i><span class="hidden-xs">banned</span>
                                    <div class="ripple-container"></div>
                                </a>
                            </li>
                            <li class="{{ set_active_filter('admins') }}">
                                <a href="{{ route('users.index', ['filter' => 'admins']) }}">
                                    <i class="fa fa-lg {{ fa('admin') }}"></i><span class="hidden-xs">admins</span>
                                    <div class="ripple-container"></div>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
                <div class="card-content table-responsive">
                    @if ($users->isNotEmpty())
                        <table class="table table-hover">
                            <thead class="text-gray">
                            <th>@sortablelink('name', 'name')</th>
                            <th>@sortablelink('username', 'username')</th>
                            @can('seeEmail', \Yap\Models\User::class)
                                <th>@sortablelink('email', 'email')</th>
                            @endcan
                            <th>@sortablelink('created_at', 'registered at')</th>
                            <th>@sortablelink('updated_at', 'last active at')</th>
                            <th class="col-xs-4">Actions</th>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name ?? config('yap.placeholders.name') }}</td>
                                    <td>{{ $user->username }}</td>
                                    @can('seeEmail', \Yap\Models\User::class)
                                        <td>{{ $user->email }}</td>
                                    @endcan
                                    <td>{!! date_with_hovertip($user->created_at) !!}</td>
                                    <td>{!! date_with_hovertip($user->updated_at) !!}</td>
                                    <td>
                                        @can('manage', $user)
                                            @if($user->isBanned())
                                                @include('components.html.fa-button', ['href' => '#', 'tooltip' => 'Unban user', 'class' => 'unban-user btn btn-success btn-xs'.disabledIf(!$current->is($user)), 'icon' => fa('ban'), 'customAttributes' => 'data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#remove-ban'])])
                                            @else
                                                @includeWhen(!$user->is_admin, 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Promote user to administrator', 'class' => 'promote-user btn btn-default btn-xs'.disabledIf(!$current->is($user)), 'icon' => fa('promote'), 'customAttributes' => 'data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#promote'])])
                                                @includeWhen($user->is_admin, 'components.html.fa-button', ['href' => '#', 'tooltip' => 'Remove user from administrators', 'class' => 'demote-user btn btn-warning btn-xs'.disabledIf(!$current->is($user)), 'icon' => fa('demote'), 'customAttributes' => 'data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#demote'])])
                                                @include('components.html.fa-button', ['href' => '#', 'tooltip' => 'Ban user', 'class' => 'ban-user btn btn-danger btn-xs'.disabledIf(!$current->is($user)), 'icon' => fa('ban'), 'customAttributes' => 'data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#ban'])])
                                            @endif
                                        @endcan
                                        @can('assignProjects', \Yap\Models\User::class)
                                            @include('components.html.fa-button', ['href' => '#', 'tooltip' => 'Add user to project', 'class' => 'add-user btn btn-info btn-xs', 'icon' => fa('add'), 'customAttributes' => 'data-reload="false" data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#add'])])
                                        @endcan
                                        @can('unassignProjects', \Yap\Models\User::class)
                                            @include('components.html.fa-button', ['href' => '#', 'tooltip' => 'Remove user from project', 'class' => 'remove-user btn btn-danger btn-xs', 'icon' => fa('remove'), 'customAttributes' => 'data-username='.$user->username.' data-help='.route('docs', ['page' => 'detail#remove'])])
                                        @endcan
                                        @include('components.html.fa-button', ['href' => route('users.show', ['user' => $user->username]), 'tooltip' => 'Detail', 'icon' => fa('detail')])
                                        @include('components.html.fa-button', ['href' => route('switch.github.user', ['user' => $user]), 'tooltip' => 'Profile on GitHub', 'class' => 'btn btn-xs bg-black', 'icon' => fa('github')])
                                        @include('components.html.taiga-button', ['href' => route('switch.taiga.user', ['user' => $user]), 'tooltip' => 'Profile on Taiga', 'class' => 'btn btn-xs btn-grey'])
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-centered">
                            {!! $users->appends(\Request::except('page'))->render() !!}
                        </div>
                    @else
                        @include('partials.no-records')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
