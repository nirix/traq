@extends('layouts.project')

@section('content')
    <div class="container">
        <h1 class="page-title">
            {{ __('projects.settings') }}
        </h1>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('projects.settings.save', ['project' => $project]) }}">
                    @csrf
                    <input type="hidden" name="_method" value="patch">

                    @include('projects/_form', ['project' => $project])

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            {{ __('forms.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
