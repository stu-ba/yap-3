@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-md-offset-2 col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'projects#create'])
                    <h4 class="title">Edit project {{ $project->name }}</h4>
                    <p class="category">You are allowed to edit mainly the team leaders and participants.</p>
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
                    <form action="{{ route('projects.update', ['project' => $project]) }}" method="post">
                        {{ csrf_field() }}
                        {{ method_field('patch') }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description*:</label>
                                    <textarea id="description" class="form-control" placeholder="Brief description of project..."
                                              rows="2" name="description" required>{{ old('description', $project->description) }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="team_leaders">Team leaders*:</label>
                                    <textarea id="team_leaders" class="form-control" placeholder="Comma / space separated list of team leaders (emails).."
                                              rows="2" name="team_leaders" required>{{ implode(', ', old('team_leaders', $leaderEmails ?? [])) }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="participants">Participants:</label>
                                    <textarea id="participants" class="form-control" placeholder="Comma / space separated list of participants (emails).."
                                              rows="2" name="participants">{{ implode(', ', old('participants', $participantEmails ?? [])) }}</textarea>
                                </div>
                                @if (is_null($project->github_team_id))
                                    <div class="form-group">
                                        @include('components.html.material-checkbox-old', ['field' => 'create_repository', 'label' => 'create repository on GitHub', 'default' => false])
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="archive-at">Archive at:</label>
                                    <input id="archive-at" class="form-control" type="text" name="archive_at" value="{{ old('archive_at', $project->archive_at ?? '') }}" placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }} or leave empty"/>
                                </div>
                                @push('components')
                                <script type="text/javascript">
                                    $('#archive-at').datetimepicker({
                                        inline:true,
                                        format: 'DD/MM/YYYY',
                                        calendarWeeks: true,
                                        locale: moment.locale('en', { //TODO: should use updateLocale
                                            week: {
                                                dow: 1
                                            }
                                        }),
                                        icons: {
                                            time: "fa fa-clock-o",
                                            date: "fa fa-calendar",
                                            up: "fa fa-arrow-up",
                                            down: "fa fa-arrow-down",
                                            previous: 'fa fa-chevron-left',
                                            next: 'fa fa-chevron-right',
                                        },
                                        minDate: moment(),
                                    });
                                </script>
                                @endpush
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">Update</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
