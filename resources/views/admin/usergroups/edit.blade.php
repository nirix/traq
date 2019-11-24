@extends('layouts.admin')

@section('title')
    {{ __('usergroups.edit_user_group') }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 class="page-title">
                    {{ __('usergroups.edit_user_group') }}
                </h1>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.user-groups.update', ['user_group' => $userGroup]) }}">
                            @csrf
                            <input type="hidden" name="_method" value="patch">

                            @include('admin/usergroups/_form', ['userGroup' => $userGroup])

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('forms.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
