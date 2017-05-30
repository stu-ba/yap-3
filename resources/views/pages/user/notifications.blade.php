@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'users#notifications'])
                    <h4 class="title">Your notifications</h4>
                    <p class="category">Notifications may be filtered.</p>
                </div>
                <div class="card-filter">
                    <span class="filter-title hidden-xs">Filter by</span>
                    <ul class="nav nav-tabs">
                        <li class="{{ set_active_filter('unread', ['read', 'all']) ?? 'active' }}">
                            <a href="{{ route('users.notifications', ['filter' => 'unread']) }}">
                                <i class="fa fa-lg {{ fa('unread') }}"></i><span class="hidden-xs">unread</span>
                                <div class="ripple-container"></div>
                            </a>
                        </li>
                        <li class="{{ set_active_filter('read') }}">
                            <a href="{{ route('users.notifications', ['filter' => 'read']) }}">
                                <i class="fa fa-lg {{ fa('read') }}"></i><span class="hidden-xs">read</span>
                                <div class="ripple-container"></div>
                            </a>
                        </li>
                        <li class="{{ set_active_filter('all') }}">
                            <a href="{{ route('users.notifications', ['filter' => 'all']) }}">
                                <i class="fa fa-lg {{ fa('all') }}"></i><span class="hidden-xs">all</span>
                                <div class="ripple-container"></div>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-content table-responsive">
                    @if ($notifications->isNotEmpty())
                        <table class="table table-hover">
                            <thead class="text-gray">
                            <th>Message</th>
                            <th>Generated at</th>
                            <th>Read at</th>
                            </thead>
                            <tbody>
                            @foreach ($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->data['message']}}</td>
                                    <td>{!! date_with_hovertip($notification->created_at) !!}</td>
                                    <td>{!! date_with_hovertip($notification->read_at) !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-centered">
                            {!! $notifications->appends(\Request::except('page'))->render() !!}
                        </div>
                    @else
                        @include('partials.no-records')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
