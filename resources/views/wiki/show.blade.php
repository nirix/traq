@extends('layouts.project')

@section('title')
    {{ $page->title }} - {{ $project->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
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
