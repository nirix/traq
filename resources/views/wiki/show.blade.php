@extends('layouts.project')

@section('title')
    {{ $page->title }} - {{ $project->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="float-right">
                    <div class="btn-group">
                        <a href="{{ route('wiki.revisions', ['project' => $project, 'wiki' => $page]) }}" class="btn btn-sm btn-outline-dark" title="{{ __('wiki.revisions') }}">
                            <i class="far fa-fw fa-file-alt"></i>
                            <span class="">{{ __('wiki.revisions') }}</span>
                        </a>
                        @can('update', $page)
                        <a href="{{ route('wiki.edit', ['project' => $project, 'wiki' => $page]) }}" class="btn btn-sm btn-outline-warning" title="{{ __('wiki.edit') }}">
                            <i class="fas fa-fw fa-pencil-alt"></i>
                            <span class="sr-only">{{ __('wiki.edit') }}</span>
                        </a>
                        @endcan
                        @can('delete', $page)
                        <a href="{{ route('wiki.destroy', ['project' => $project, 'wiki' => $page]) }}" class="btn btn-sm btn-outline-danger" title="{{ _('wiki.delete') }}">
                            <i class="fas fa-fw fa-trash"></i>
                            <span class="sr-only">{{ __('wiki.delete') }}</span>
                        </a>
                        @endcan
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('wiki.pages', ['project' => $project]) }}" class="btn btn-sm btn-outline-dark">
                            <i class="fas fa-fw fa-book"></i>
                            <span>{{ __('wiki.pages') }}</span>
                        </a>
                        @can('create', \Traq\Wiki::class)
                        <a href="{{ route('wiki.create', ['project' => $project]) }}" class="btn btn-sm btn-outline-dark">
                            <i class="fas fa-fw fa-plus"></i>
                            <span>{{ __('wiki.new_page') }}</span>
                        </a>
                        @endcan
                    </div>
                </div>
                <h1 class="page-title">
                    {{ $page->title }}
                </h1>
                <div class="card">
                    <div class="card-body">
                        @parsedown($page->latestRevision()->content)
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
