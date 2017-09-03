<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta name="viewport" content="maximum-scale=1,width=device-width,initial-scale=1,user-scalable=0">
    <meta name="referrer" content="origin-when-cross-origin">

    <meta content="{{ config('app.name') }}" name="author" />
    <meta property="fb:app_id" content="{{ config('services.facebook.client_id') }}" />

    <link type="application/opensearchdescription+xml" rel="search" href="/osd{{ config('app.locale') }}.xml">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ MetaTag::get('title') }}</title>
    {!! MetaTag::tag('description') !!}
    {!! MetaTag::tag('image') !!}
    {!! MetaTag::openGraph() !!}

    <!-- Browser theme styling -->
    <meta content="#ffffff" name="msapplication-TileColor" />
    <meta content="#ffffff" name="theme-color" />

    <!-- Hreflangs https://moz.com/learn/seo/hreflang-tag -->
    <link rel="alternate" href="https://www.nixler.ge/" hreflang="ka-ge" />
    <link rel="alternate" href="https://www.nixler.pl/" hreflang="pl-pl" />

    <!-- Stylesheets -->
    <link href="/css/app.css" rel="stylesheet">
    @yield('styles')

    <!-- Scripts -->
    <script>
        window.nx = <?php echo capsule('frontend')->toJson(); ?>;
    </script>

    <link id="favicon" rel="icon" type="image/png" sizes="64x64" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAh1BMVEUAAABVgKptkrZVgL9bgLZggL9chbhcgLtagbxdgL1cgLxbgb1bgLxdgb1agbtcgbtdgLxcgb1cgLxdgLtcgLxdgL1cgLxbgLxcgLxdgLxcgLxdgLxcgLxcgL1cgLxdgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxG47sgAAAALHRSTlMABgcMDhgZQEFCSElMTU91dnd6fIWHiImys7S+v8Dg4uPk5eby8/T4+fz9/qPzmuoAAACNSURBVHgBvZDdEoFQFEY3KRLyQxEpIT/W+z+f2XSR3RgzXVg335m1zrk58ptJWpa7cdNHvFg27nMJPW96ZWRCSqgzY2vCGVfH5VSTQc4HeVCFPYasCnfAkaOqg/R0qnAD+hKrWYmvU3uxlm5cFFFHEhOYv88LbGAzcBw/oR4eWFoH/hDyb58YZMYPpQ1PpmIwDYJOKekAAAAASUVORK5CYII=">

</head>
<body class="@yield('body_class')">

    <div id="app" class="_db" ng-app="nx">
        @yield('app')
    </div>

    <script src="{{ url('js/app.js') }}"></script>


    @if(app()->environment('production', 'development', 'local'))
        @include('/partials/analytics')
    @endif

    @if(config('app.country') != 'GE')
        @include('cookieConsent::index')
    @endif
</body>
</html>