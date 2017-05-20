<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>

    <!-- Meta information -->
    <meta content="" charset="utf-8" />

    <!-- Always force latest IE rendering engine or request Chrome Frame -->
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
    <meta content="width=device-width,initial-scale=1.0" name="viewport" />

    <meta content="{{ config('app.name') }}" name="author" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {!! app('seotools')->metatags()->generate() !!}
    {!! app('seotools')->opengraph()->generate() !!}

    <!-- Browser theme styling -->
    <meta content="#ffffff" name="msapplication-TileColor" />
    <meta content="#ffffff" name="theme-color" />

    <!-- Stylesheets -->
    <link href="/css/app.css" rel="stylesheet">
    @yield('styles')


    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
            ]); ?>
    </script>

</head>
<body class="@yield('body_class')">
    
    <div id="app" class="_db" ng-app="nx">
    @yield('app')
    </div>

    <script src="{{ url('js/app.js') }}"></script>
    @include('cookieConsent::index')
</body>
</html>