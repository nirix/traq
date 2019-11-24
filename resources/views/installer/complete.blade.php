@extends('layouts.installer')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="alert alert-success text-center">
                    Congratulations! Traq is now installed.
                </div>
                <div class="text-center">
                    <a href="{{ route('login') }}">Login</a> to manage your projects.
                </div>
            </div>
        </div>
    </div>
@endsection
