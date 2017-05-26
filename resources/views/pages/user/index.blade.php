@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'users#listing'])
                    <a href="{{ route('invitations.create') }}" class="btn btn-xs btn-white pull-right invite-user"><i class="fa {{ fa('invite') }}"></i> <span class="hidden-xs">Invite</span></a>
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
                    </ul>
                </div>
                <div class="card-content table-responsive">
                    @if ($users->isNotEmpty())
                    <table class="table table-hover">
                        <thead class="text-gray">
                        {{--<th>@sortablelink('id', 'ID')</th>--}}
                        <th>@sortablelink('name', 'name')</th>
                        <th>@sortablelink('username', 'username')</th>
                        <th>@sortablelink('created_at', 'registered at')</th>
                        <th>@sortablelink('updated_at', 'last active at')</th>
                        <th>Actions</th>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    {{--<td>{{ $user->id }}</td>--}}
                                    <td>{{ $user->name ?? config('yap.placeholders.name') }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{!! date_with_hovertip($user->created_at) !!}</td>
                                    <td>{!! date_with_hovertip($user->updated_at) !!}</td>
                                    <td>
                                        @include('components.html.fa-button', ['href' => route('users.show', ['user' => $user->username]), 'tooltip' => 'Detail', 'icon' => fa('profile')])
                                        @include('components.html.fa-button', ['href' => route('users.edit', ['user' => $user->username]), 'tooltip' => 'Edit user', 'class' => 'btn btn-primary btn-xs', 'icon' => fa('edit')])
                                        @include('components.html.fa-button', ['href' => 'https://github.com/'.$user->username, 'tooltip' => 'Profile on GitHub', 'class' => 'btn btn-xs bg-black external', 'icon' => fa('github')])
                                        @include('components.html.taiga-button', ['href' => route('switch.user', ['user' => $user]), 'tooltip' => 'Profile on Taiga', 'class' => 'btn btn-xs btn-grey'])
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
