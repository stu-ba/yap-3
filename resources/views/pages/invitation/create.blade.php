@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'administration-invitations#tedious'])
                    <h4 class="title">Create invitation</h4>
                    <p class="category">Check options as you see fit.</p>
                </div>
                <div class="card-content">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('invitations.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group label-floating {{ !is_null(old('email', $email)) ? '' : 'is-empty' }}">
                                    <label class="control-label">Email</label>
                                    <input type="email" value="{{ old('email', $email) }}" class="form-control" name="email" required autofocus autocomplete="off">
                                    <span class="material-input"></span></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12"><label>Options</label></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @include('components.html.material-checkbox-old', ['field' => 'admin', 'label' => 'Should user be administrator?'])
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @include('components.html.material-checkbox-old', ['field' => 'indefinite', 'label' => 'Should invitation last forever?'])
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @include('components.html.material-checkbox-old', ['field' => 'dont_send', 'label' => 'Should invitation email be suppressed?'])
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @include('components.html.material-checkbox-old', ['field' => 'force_resend', 'label' => 'Should invitation email be force resend?'])
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">Invite</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'administration-invitations#recent'])
                    <h4 class="title">Invitations listing</h4>
                    <p class="category">Most recent invitations are on top.</p>
                </div>
                <div class="card-content table-responsive">
                    @if ($invitations->isNotEmpty())
                        <table class="table table-hover">
                            <thead class="text-gray">
                            <th>Email</th>
                            <th>Invited by</th>
                            <th>Invited at</th>
                            <th>Updated at</th>
                            <th>Valid until</th>
                            </thead>
                            <tbody>
                            @foreach ($invitations as $invitation)
                                <tr>
                                    <td>{{ $invitation->email }}</td>
                                    <td>{{ $invitation->inviter->name ?? $invitation->inviter->username }}</td>
                                    <td>{!! date_with_hovertip($invitation->created_at) !!}</td>
                                    <td>{!! date_with_hovertip($invitation->updated_at) !!}</td>
                                    <td>{!! date_with_hovertip($invitation->valid_until, 'top', $invitation->updated_at) !!}</td>
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