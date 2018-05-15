<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>代理商后台</title>

    <!-- Bootstrap -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/agent.css') }}" rel="stylesheet">

    @if(!empty($stylesheets))
        @foreach($stylesheets as $style)
            <link href="{{ $style }}" rel="stylesheet">
        @endforeach
    @endif
    
    <!-- HTML5 shim 和 Respond.js 是为了让 IE8 支持 HTML5 元素和媒体查询（media queries）功能 -->
    <!-- 警告：通过 file:// 协议（就是直接将 html 页面拖拽到浏览器中）访问页面时 Respond.js 不起作用 -->
    <!--[if lt IE 9]>
        <script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    @if(!empty($headjs))
        @foreach($headjs as $js)
            <script src="{{ $js }}"></script>
        @endforeach
    @endif
</head>
<body>
    <div id="bodyContainer">
        <div id="siderContainer">
            @include('agent.layouts.sider')
        </div>
        
        <div id="contentContainer">
            @yield('content')
        </div>
    </div>

    @if(!empty($javascripts))
        @foreach($javascripts as $js)
            <script src="{{ $js }}"></script>
        @endforeach
    @endif
</body>
</html>