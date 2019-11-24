@extends('layouts.project')

@section('title')
    {{ __('milestones.new_milestone') }} - {{ $project->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 class="page-title">
                    {{ __('milestones.new_milestone') }}
                </h1>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('milestones.store', ['project' => $project]) }}">
                            @csrf

                            @include('milestones/_form', ['milestone' => $milestone])

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
