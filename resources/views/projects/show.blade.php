@extends('layouts.project')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @parsedown($project->description)
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
