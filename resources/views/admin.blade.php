<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>推广管理系统</title>

    <style type="text/css">
        #example {
            display: flex;
            flex: 1;
            height: 100%;
        }
    </style>

    <!-- Scripts -->
    <script src="{{ asset('js/admin.js') }}" defer></script>
</head>
<body>

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <div id="example"></div>

</body>
</html>
