<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta name="viewport" content="maximum-scale=1,width=device-width,initial-scale=1,user-scalable=0">
    <meta name="referrer" content="origin-when-cross-origin">

    <meta content="{{ config('app.name') }}" name="author" />

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-MH8TJZW');</script>
    <!-- End Google Tag Manager -->

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

    <!-- Stylesheets -->
    <link href="/css/app.css" rel="stylesheet">
    @yield('styles')


    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    <link id="favicon" rel="icon" type="image/png" sizes="64x64" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAh1BMVEUAAABVgKptkrZVgL9bgLZggL9chbhcgLtagbxdgL1cgLxbgb1bgLxdgb1agbtcgbtdgLxcgb1cgLxdgLtcgLxdgL1cgLxbgLxcgLxdgLxcgLxdgLxcgLxcgL1cgLxdgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxG47sgAAAALHRSTlMABgcMDhgZQEFCSElMTU91dnd6fIWHiImys7S+v8Dg4uPk5eby8/T4+fz9/qPzmuoAAACNSURBVHgBvZDdEoFQFEY3KRLyQxEpIT/W+z+f2XSR3RgzXVg335m1zrk58ptJWpa7cdNHvFg27nMJPW96ZWRCSqgzY2vCGVfH5VSTQc4HeVCFPYasCnfAkaOqg/R0qnAD+hKrWYmvU3uxlm5cFFFHEhOYv88LbGAzcBw/oR4eWFoH/hDyb58YZMYPpQ1PpmIwDYJOKekAAAAASUVORK5CYII=">

</head>
<body class="@yield('body_class')">

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WVZNJHQ"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div id="app" class="_db" ng-app="nx">
        @yield('app')
    </div>

    <script src="{{ url('js/app.js') }}"></script>
    @include('cookieConsent::index')
</body>
</html>

{{-- 
<!-- Error reporting  onerror="_failed(this)" -->
    <script>
      var _failed = function(n) {
        n = n.src || n.href;
        var e = function(n) {
            return (n = /^https?:\/\/[^\/]+/.exec(n)) ? n[0] : void 0
          },
          e = e(n) || e(location.href) || "unknown";
        //ga("send", "event", "Load Failure", e, n)
      };
    </script>
    <!-- End Error reporting -->

<link rel="mask-icon" href="https://a.trellocdn.com/images/225c8d1cf8bbf74add43d86f2c84bd28/pinned-tab-icon.svg" color="#0079BF">

<link rel="apple-touch-icon-precomposed" sizes="152x152" href="https://a.trellocdn.com/images/ios/0307bc39ec6c9ff499c80e18c767b8b1/apple-touch-icon-152x152-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="https://a.trellocdn.com/images/ios/0dbf6daf256eb7678e5e2185d5146165/apple-touch-icon-144x144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="120x120" href="https://a.trellocdn.com/images/ios/a68051b5c9144abe859ba11e278bbceb/apple-touch-icon-120x120-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="https://a.trellocdn.com/images/ios/7f4a80b64fd8fd99840b1c08d9b45a04/apple-touch-icon-114x114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="https://a.trellocdn.com/images/ios/91a3a04ec68a6090380156f847c082bf/apple-touch-icon-72x72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="https://a.trellocdn.com/images/ios/8de2074e8a785dd5d498f8f956267478/apple-touch-icon-precomposed.png">


</head> --}}
