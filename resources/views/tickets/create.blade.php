@extends('layouts.project')

@section('title')
    {{ __('tickets.new_ticket') }} - {{ $project->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 class="page-title">
                    {{ __('tickets.new_ticket') }}
                </h1>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('tickets.store', ['project' => $project]) }}">
                            @csrf

                            @include('tickets/_form', ['ticket' => $ticket])

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
