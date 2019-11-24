@extends('layouts.installer')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form action="{{ route('installer_database') }}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center text-danger">
                                {{ $message }}
                            </div>
                            <div class="text-center">
                                Check the database settings in the <code>.env</code> file and try again.
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-primary">
                            Try Again
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
