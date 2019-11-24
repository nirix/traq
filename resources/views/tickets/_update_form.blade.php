@can('commentOrUpdate', $ticket)
    <section class="mb-4">
        <h2 class="section-title mb-0">{{ __('tickets.update_ticket') }}</h2>
        <div class="card">
            <div class="card-body">
                <div class="container-fluid">
                    <form action="{{ route('tickets.update', ['project' => $project, 'ticket' => $ticket]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="patch">

                        @can('comment', $ticket)
                        <div class="form-group">
                            <label for="comment">{{ __('tickets.comment') }}</label>
                            <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror">{{ old('comment') }}</textarea>

                            @error('comment')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        @endcan

                        @can('update', $ticket)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">{{ __('tickets.status') }}</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                        @foreach(\Traq\Status::all() as $status)
                                            <option value="{{ $status->id }}" @if(old('status', $ticket->status_id) == $status->id) selected @endif>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">{{ __('tickets.type') }}</label>
                                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                                        <option value="">{{ __('tickets.select_type') }}</option>
                                        @foreach(\Traq\Type::all() as $type)
                                            <option value="{{ $type->id }}" @if(old('type', $ticket->type_id) == $type->id) selected @endif>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="priority">{{ __('tickets.priority') }}</label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror">
                                        @foreach(\Traq\Priority::all() as $priority)
                                            <option value="{{ $priority->id }}" @if(old('priority', $ticket->priority_id) == $priority->id) selected @endif>
                                                {{ $priority->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('priority')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="version">{{ __('tickets.milestone') }}</label>
                                    <select name="milestone" id="milestone" class="form-control @error('milestone') is-invalid @enderror">
                                        @if($ticket->ticket_id && $ticket->milestone->isClosed())
                                            <option value="{{ $ticket->milestone->id }}" selected>
                                                {{ $ticket->milestone->name }}
                                            </option>
                                        @endif
                                        @foreach($project->activeMilestones as $milestone)
                                            <option value="{{ $milestone->id }}" @if(old('milestone', $ticket->milestone_id) == $milestone->id) selected @endif>
                                                {{ $milestone->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('milestone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="version">{{ __('tickets.version') }}</label>
                                    <select name="version" id="version" class="form-control @error('version') is-invalid @enderror">
                                        <option value=""></option>
                                        @foreach($project->milestones as $version)
                                            <option value="{{ $version->id }}" @if(old('version', $ticket->version_id) == $version->id) selected @endif>
                                                {{ $version->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('version')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="summary">{{ __('tickets.summary') }}</label>
                                    <input id="summary" type="text" class="form-control @error('summary') is-invalid @enderror" name="summary" value="{{ old('summary', $ticket->summary) }}" autocomplete="summary">

                                    @error('summary')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary">{{ __('forms.update') }}</button>
                        </div>
                        @else
                            <div class="text-center">
                                <button class="btn btn-primary">{{ __('forms.post') }}</button>
                            </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </section>
@endcan
