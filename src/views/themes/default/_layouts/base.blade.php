<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Home')</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta property="orocms:uuid" content="{!! csrf_token() !!}">

    <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,100italic,300italic,400italic,500italic,700italic' rel='stylesheet' type='text/css'>
    <style>
    body {
        font-family: 'Roboto', serif;
        font-size: 18px;
        font-weight: 300;
        border-top: 5px solid #ccc;
        color: #333;
        margin: 0; padding: 0; }
    h1, h1 {
        margin: 0 0 10px 0; padding: 0; }
    .wrapper {
        padding: 10px; }
    </style>

    @yield('header')
</head>
<body>
    <div class="wrapper">
        @yield('content')
    </div>
</body>
</html>
