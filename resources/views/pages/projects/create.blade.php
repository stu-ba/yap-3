@extends('layouts.yap')
@section('content')
    <div class="row">
        <div class="col-md-offset-2 col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    @include('components.html.card-support', ['page' => 'projects#create'])
                    <h4 class="title">Create project</h4>
                    <p class="category">Fill out all required fields.</p>
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
                    <form action="{{ route('projects.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group label-floating {{ !is_null(old('name')) ? '' : 'is-empty' }}">
                                    <label class="control-label">Name</label>
                                    <input type="text" value="{{ old('name') }}" class="form-control" name="name"
                                           required autofocus autocomplete="off" autocorrect="off" autocapitalize="off"
                                           spellcheck="false">
                                </div>
                                <div class="form-group">
                                    <label for="description">Description*:</label>
                                    <textarea id="description" class="form-control" placeholder="Brief description of project..."
                                              rows="2" name="description" required>{{ old('description') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="team_leaders">Team leaders*:</label>
                                    <textarea id="team_leaders" class="form-control" placeholder="Comma / space separated list of team leaders (emails).."
                                              rows="2" name="team_leaders" required>{{ implode(', ', old('team_leaders', []) ?? []) }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="participants">Participants:</label>
                                    <textarea id="participants" class="form-control" placeholder="Comma / space separated list of participants (emails).."
                                              rows="2" name="participants">{{ implode(', ', old('participants', []) ?? []) }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="type">Project type*:</label>
                                    <select class="form-control" name="project_type_id" id="type" required>
                                        @foreach ($projectTypes as $typeId => $type)
                                            <option value="{{ $typeId }}"
                                                    @if ($typeId == old('project_type_id'))
                                                    selected="selected"
                                                    @endif
                                            >{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    @include('components.html.material-checkbox-old', ['field' => 'repository', 'label' => 'create repository on GitHub'])
                                </div>
                                <div class="form-group">
                                    <label for="type">Archive at:</label>
                                    <input class="datepicker form-control" type="text" name="archive_at" value="{{ old('archive_at') }}" placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}"/>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">Create</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
