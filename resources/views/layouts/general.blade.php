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

    <!-- Stylesheets -->
    <link href="/css/app.css" rel="stylesheet">
    @yield('styles')


    <!-- Scripts -->
    <script>
        window.nx = <?php echo json_encode([
            'csrfToken' => csrf_token(),
            'currency' => config('app.currency'),
            'currencySymbol' => trim(money(config('app.currency'))),
        ]); ?>;
    </script>

    <link id="favicon" rel="icon" type="image/png" sizes="64x64" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAh1BMVEUAAABVgKptkrZVgL9bgLZggL9chbhcgLtagbxdgL1cgLxbgb1bgLxdgb1agbtcgbtdgLxcgb1cgLxdgLtcgLxdgL1cgLxbgLxcgLxdgLxcgLxdgLxcgLxcgL1cgLxdgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxcgLxG47sgAAAALHRSTlMABgcMDhgZQEFCSElMTU91dnd6fIWHiImys7S+v8Dg4uPk5eby8/T4+fz9/qPzmuoAAACNSURBVHgBvZDdEoFQFEY3KRLyQxEpIT/W+z+f2XSR3RgzXVg335m1zrk58ptJWpa7cdNHvFg27nMJPW96ZWRCSqgzY2vCGVfH5VSTQc4HeVCFPYasCnfAkaOqg/R0qnAD+hKrWYmvU3uxlm5cFFFHEhOYv88LbGAzcBw/oR4eWFoH/hDyb58YZMYPpQ1PpmIwDYJOKekAAAAASUVORK5CYII=">

</head>
<body class="@yield('body_class')">

    <div id="app" class="_db" ng-app="nx">
        @yield('app')
    </div>

    <script src="{{ url('js/app.js') }}"></script>

    @if(app()->environment('production', 'development'))
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-72740986-2', 'auto');
      ga('send', 'pageview');
    </script>

    @if(!request()->isRobot())
    <script>
    window['_fs_debug'] = false;
    window['_fs_host'] = 'fullstory.com';
    window['_fs_org'] = '54G1K';
    window['_fs_namespace'] = 'FS';
    (function(m,n,e,t,l,o,g,y){
        if (e in m && m.console && m.console.log) { m.console.log('FullStory namespace conflict. Please set window["_fs_namespace"].'); return;}
        g=m[e]=function(a,b){g.q?g.q.push([a,b]):g._api(a,b);};g.q=[];
        o=n.createElement(t);o.async=1;o.src='https://'+_fs_host+'/s/fs.js';
        y=n.getElementsByTagName(t)[0];y.parentNode.insertBefore(o,y);
        g.identify=function(i,v){g(l,{uid:i});if(v)g(l,v)};g.setUserVars=function(v){g(l,v)};
        g.identifyAccount=function(i,v){o='account';v=v||{};v.acctId=i;g(o,v)};
        g.clearUserCookie=function(c,d,i){if(!c || document.cookie.match('fs_uid=[`;`]*`[`;`]*`[`;`]*`')){
        d=n.domain;while(1){n.cookie='fs_uid=;domain='+d+
        ';path=/;expires='+new Date(0).toUTCString();i=d.indexOf('.');if(i<0)break;d=d.slice(i+1)}}};
    })(window,document,window['_fs_namespace'],'script','user');
    </script>
    @endif
    @endif

    @if(config('app.country') != 'GE')
        @include('cookieConsent::index')
    @endif
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
