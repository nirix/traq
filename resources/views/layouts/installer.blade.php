<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Traq Installer</title>

    <!-- Scripts -->
    <script src="{{ asset('js/installer.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/installer.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm rounded">
                        <div class="container">
                            <a class="navbar-brand" href="@yield('navbar_brand_url', url('/'))">
                                Traq Installer
                            </a>
                            <span class="navbar-text">
                                {{ $step }}
                            </span>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                        </div>
                    </nav>

                    <div class="text-center mt-4 installer-steps">
                        <i class="fas fa-file-alt @if($step === 'License Agreement')@else text-muted @endif" title="License Agreement"></i>
                        <i class="fas fa-folder-open @if($step === 'Filesystem Permissions')@else text-muted @endif" title="Filesystem Permissions"></i>
                        <i class="fas fa-database @if($step === 'Database Installation')@else text-muted @endif" title="Database Installation"></i>
                        <i class="fas fa-user @if($step === 'Admin User')@else text-muted @endif" title="Admin User"></i>
                    </div>

                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block mt-4 mb-0">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <main class="py-4">
            @yield('content')
        </main>

        <div id="footer" class="text-center">
            Powered by <a href="https://traq.io">Traq</a> v{{ config('traq.version', '4-dev') }}
        </div>
    </div>
</body>
</html>
