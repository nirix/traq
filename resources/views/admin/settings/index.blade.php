@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 class="page-title">
                    {{ __('admin.settings') }}
                </h1>

                <form method="POST" action="{{ route('admin.settings.save') }}">
                    @csrf
                    <input type="hidden" name="_method" value="patch">

                    <section>
                        <div class="card">
                            <div class="card-body">
                                <h2 class="card-title">
                                    {{ __('admin.traq_settings') }}
                                </h2>

                                <div class="form-group row">
                                    <label for="traq_name" class="col-md-4 col-form-label text-md-right">{{ __('admin.traq_name') }}</label>

                                    <div class="col-md-6">
                                        <input id="traq_name" type="text" class="form-control @error('traq_name') is-invalid @enderror" name="traq_name" value="{{ old('traq_name', config('settings.traq_name')) }}" required autocomplete="traq_name">

                                        @error('traq_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">
                            {{ __('forms.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
