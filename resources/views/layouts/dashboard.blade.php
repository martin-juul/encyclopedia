<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Encyclopedia') }}</title>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500" rel="stylesheet">

    <link rel="dns-prefetch" href="//stackpath.bootstrapcdn.com">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="{{ mix('css/dashboard.css') }}" rel="stylesheet">

    <script src="{{ mix('js/dashboard.js') }}" defer></script>
</head>
<body>

<div id="app" class="page-container chiller-theme toggled">
    @include('dashboard.partials.sidebar')

    <main class="page-content">
        <div class="container-fluid">
        @yield('content')
        </div>
    </main>
</div>
</body>
</html>
