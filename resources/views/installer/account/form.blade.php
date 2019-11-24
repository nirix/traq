@extends('layouts.installer')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form action="{{ route('installer_user_create') }}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            @include('auth._register_form')
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-primary">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
