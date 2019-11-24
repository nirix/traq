@extends('layouts.app')

@section('title')
    {{ __('projects.new_project') }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('projects.new_project') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.projects.store') }}">
                            @csrf

                            @include('projects/_form', ['project' => $project])

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('forms.create') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
