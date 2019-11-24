<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('milestones.name') }}</label>

    <div class="col-md-6">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $milestone->name) }}" required autocomplete="name" autofocus>

        @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="codename" class="col-md-4 col-form-label text-md-right">{{ __('milestones.codename') }}</label>

    <div class="col-md-6">
        <input id="codename" type="text" class="form-control @error('codename') is-invalid @enderror" name="codename" value="{{ old('codename', $milestone->codename) }}" autocomplete="codename">

        @error('codename')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="slug" class="col-md-4 col-form-label text-md-right">{{ __('milestones.slug') }}</label>

    <div class="col-md-6">
        <input id="slug" type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $milestone->slug) }}" required autocomplete="slug">

        @error('slug')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('milestones.status') }}</label>

    <div class="col-md-6">
        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
            <option value="0" @if(old('status', $milestone->status) === Traq\Milestone::STATUS_ACTIVE) selected @endif>Active</option>
            <option value="1" @if(old('status', $milestone->status) === Traq\Milestone::STATUS_COMPLETED) selected @endif>Completed</option>
        </select>

        @error('status')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="due_at" class="col-md-4 col-form-label text-md-right">{{ __('milestones.due_on') }}</label>

    <div class="col-md-6">
        <input id="due_at" type="date" class="form-control @error('due_at') is-invalid @enderror" name="due_at" value="{{ old('due_at', $milestone->slug) }}" autocomplete="due_at">

        @error('due_at')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="display_order" class="col-md-4 col-form-label text-md-right">{{ __('milestones.display_order') }}</label>

    <div class="col-md-6">
        <input id="display_order" type="text" class="form-control @error('display_order') is-invalid @enderror" name="display_order" value="{{ old('display_order', $milestone->display_order) }}" required autocomplete="display_order">

        @error('display_order')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('milestones.description') }}</label>

    <div class="col-md-6">
        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description', $milestone->description) }}</textarea>

        @error('description')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
