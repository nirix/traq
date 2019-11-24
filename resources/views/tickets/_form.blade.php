<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="type">{{ __('tickets.type') }}</label>
            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                <option value="">{{ __('tickets.select_type') }}</option>
                @foreach(\Traq\Type::all() as $type)
                    <option value="{{ $type->id }}" @if(old('type', $ticket->type_id) === $type->id) selected @endif>
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
    <div class="col-md-9">
        <div class="form-group">
            <label for="summary">{{ __('tickets.summary') }}</label>
            <input id="summary" type="text" class="form-control @error('summary') is-invalid @enderror" name="summary" value="{{ old('summary', $ticket->summary) }}" required autocomplete="summary">

            @error('summary')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label for="description">{{ __('tickets.description') }}</label>
    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $ticket->description) }}</textarea>

    @error('description')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="milestone">{{ __('tickets.milestone') }}</label>
            <select name="milestone" id="milestone" class="form-control @error('milestone') is-invalid @enderror" required>
                <option value="">{{ __('tickets.select_milestone') }}</option>
                @foreach($project->activeMilestones as $activeMilestone)
                    <option value="{{ $activeMilestone->id }}" @if(old('milestone', $activeMilestone->id) == $ticket->milestone_id) selected @endif>
                        {{ $activeMilestone->name }}
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
    <div class="col-md-6">
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
</div>
