<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('projects.name') }}</label>

    <div class="col-md-6">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $project->name) }}" required autocomplete="name" autofocus>

        @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="codename" class="col-md-4 col-form-label text-md-right">{{ __('projects.codename') }}</label>

    <div class="col-md-6">
        <input id="codename" type="text" class="form-control @error('codename') is-invalid @enderror" name="codename" value="{{ old('codename', $project->codename) }}" autocomplete="codename">

        @error('codename')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="slug" class="col-md-4 col-form-label text-md-right">{{ __('projects.slug') }}</label>

    <div class="col-md-6">
        <input id="slug" type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $project->slug) }}" required autocomplete="slug" >

        @error('slug')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="slug" class="col-md-4 col-form-label text-md-right">{{ __('projects.description') }}</label>

    <div class="col-md-6">
        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description) }}</textarea>

        @error('description')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="default_status" class="col-md-4 col-form-label text-md-right">{{ __('projects.default_status') }}</label>

    <div class="col-md-6">
        <select name="default_status" id="default_status" class="form-control @error('default_status') is-invalid @enderror">
            @foreach(\Traq\Status::all() as $status)
                <option value="{{ $status->id }}" @if(old('default_status', $project->default_status_id) == $status->id) selected @endif>
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

<div class="form-group row">
    <label for="default_priority" class="col-md-4 col-form-label text-md-right">{{ __('projects.default_priority') }}</label>

    <div class="col-md-6">
        <select name="default_priority" id="default_priority" class="form-control @error('default_priority') is-invalid @enderror">
            @foreach(\Traq\Priority::all() as $priority)
                <option value="{{ $priority->id }}" @if(old('default_priority', $project->default_priority_id) == $priority->id) selected @endif>
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

<div class="form-group row">
    <div class="col-md-6 offset-md-4">
        <input id="enable_wiki" type="checkbox" class="@error('enable_wiki') is-invalid @enderror" name="enable_wiki" value="1" @if(old('enable_wiki', $project->enable_wiki)) checked="checked" @endif>
        <label for="enable_wiki">
            {{ __('projects.enable_wiki') }}
        </label>

        @error('description')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
