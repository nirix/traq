@extends('layouts.app')

@section('title')
    {{__('auth.verify_your_email_address') }} - @parent
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('auth.verify_your_email_address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('auth.a_fresh_verification_link_has_been_sent') }}
                        </div>
                    @endif

                    {{ __('auth.before_proceeding_check_your_email') }}
                    {{ __('auth.if_you_did_not_receive_an_email') }}, <a href="{{ route('verification.resend') }}">{{ __('auth.click_here_to_request_another') }}</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
