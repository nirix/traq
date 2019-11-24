@extends('layouts.project')

@section('title')
    {{ __('milestones.edit_milestone_x', ['name' => $milestone->name]) }} - {{ $project->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 class="page-title">
                    {{ __('milestones.edit_milestone') }}
                </h1>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('milestones.update', ['project' => $project, 'milestone' => $milestone]) }}">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH">

                            @include('milestones/_form', ['milestone' => $milestone])

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('save') }}
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
