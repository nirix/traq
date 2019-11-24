<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            <label for="title">{{ __('wiki.title') }}</label>
            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $page->title) }}" required autocomplete="title">

            @error('title')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="slug">{{ __('wiki.slug') }}</label>
            <input id="slug" type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $page->slug) }}" required autocomplete="slug">

            @error('slug')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label for="content">{{ __('wiki.content') }}</label>
    <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $revision->content) }}</textarea>

    @error('content')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>
