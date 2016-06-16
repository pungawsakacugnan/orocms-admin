<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title>Control Panel | @yield('title', 'Dashboard')</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta property="orocms:uuid" content="{!! csrf_token() !!}">

    @include('admin::partials.styles')
    @yield('style')

    @stack('header')
</head>
<body class="compact-font">
    <div id="wrapper">
        <div class="spacer"></div>
        @include('admin::partials.nav')
        @yield('breadcrumb')

        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        @include('admin::partials.flashes')
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin::partials.footer')

    <script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        @stack('jquery-scripts')
    });
    </script>
</body>
</html>
