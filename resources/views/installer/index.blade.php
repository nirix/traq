@extends('layouts.installer')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form action="{{ route('installer_permissions') }}" method="post">
                    @csrf
                    <div class="card">
                        <pre id="license">{{ $license }}</pre>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-primary">
                            Accept
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
