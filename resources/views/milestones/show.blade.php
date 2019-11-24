@extends('layouts.project')

@section('title')
    {{ $milestone->name }} - {{__('Milestones') }} - @parent
@endsection

@section('content')
    <div class="container">
        <div class="float-right">
            @can('create', Traq\Milestone::class)
            <a href="{{ route('milestones.edit', ['project' => $project, 'milestone' => $milestone]) }}" class="btn btn-md btn-primary" title="{{ __('Edit') }}">
                <i class="fas fa-fw fa-pencil-alt"></i>
                <span class="sr-only">{{ __('Edit') }}</span>
            </a>
            @endcan
        </div>
        <h1 class="page-title">
            {{ $milestone->name }}
        </h1>
        <div class="card">
            <div class="card-body">
                @include('milestones/_progress_bar', ['milestone' => $milestone])
                {{ $milestone->description }}
            </div>
        </div>
    </div>
@endsection
