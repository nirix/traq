@extends('layouts.project')

@section('title')
    {{ __('wiki.new_page') }} - {{ $project->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 class="page-title">
                    {{ __('wiki.new_page') }}
                </h1>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('wiki.store', ['project' => $project]) }}">
                            @csrf

                            @include('wiki/_form', ['page' => $page, 'revision' => $revision])

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('forms.create') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
