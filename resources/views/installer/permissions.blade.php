@extends('layouts.installer')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <ul class="list-group list-group-flush">
                            @foreach ($permissions as $permission)
                                @if($permission['valid'])
                                    <li class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <span>{{ $permission['path'] }}</span>
                                            <span class="text-success">Writable</span>
                                        </div>
                                    </li>
                                @else
                                    <li class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <span>{{ $permission['path'] }}</span>
                                            <span class="text-danger">Unwritable</span>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="text-center mt-4">
                        @if($errors)
                            <form action="{{ route('installer_permissions') }}" method="post">
                                @csrf
                                <button class="btn btn-primary">
                                    Check Again
                                </button>
                            </form>
                        @else
                            <form action="{{ route('installer_database') }}" method="post">
                                @csrf
                                <button class="btn btn-primary">
                                    Next
                                </button>
                            </form>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
