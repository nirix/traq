@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    @forelse ($projects as $project)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <a href="{{ route('project.show', ['project' => $project]) }}">
                                        {{ $project->name }}
                                    </a>
                                </div>
                                <div class="card-body">
                                    {{ $project->description }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-md-6 offset-md-3">
                            <div class="alert alert-secondary text-center">
                                {{ __('projects.no_projects') }}
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
