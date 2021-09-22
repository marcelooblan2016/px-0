<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{--        <meta http-equiv="Content-Security-Policy" content="default-src 'self' *.bootstrapcdn.com *.stripe.com *.stripe.network *.googleapis.com 'unsafe-inline'; style-src *.stripe.com https://* 'unsafe-inline';  font-src https://*; img-src https://*; worker-src https://*; frame-src https://*; script-src 'unsafe-inline' 'unsafe-eval' https://* *.stripe.com;">--}}
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $suffix =  vsprintf("| %s %s", [
                config('app.name'),
                ''
            ]);

            if (($__env->yieldContent('no_suffix_title') == true)) {
                $suffix = '';
            }
        @endphp

        <title>@yield('title'){{ $suffix }}</title>

        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

        @yield('meta')

        <!-- Fonts -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Glory:wght@400;500&display=swap" rel="stylesheet">
        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        @yield('stylesheets')
        @stack('extra-stylesheets')
    </head>


    <body>
        <div id="app">
            @include('layouts.header.menu')
            <main>
                @yield('content')
            </main>
            @include('layouts.footer.index')
        </div>

        @yield('pre-app-js')
        <script src="{{ mix('js/app.js') }}"></script>
        @yield("javascript")
        @stack('end-javascript')
    </body>
</html>
