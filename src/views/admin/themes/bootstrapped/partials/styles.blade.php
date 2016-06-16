<link href="{{ elixir( theme('admin.assets', 'css/app.css') ) }}" rel="stylesheet">
<script src="{{ elixir( theme('admin.assets', 'js/app.js') ) }}"></script>

<link href="{{ asset( theme('admin.assets', 'js/bootstrap-select/bootstrap-select.min.css') ) }}" rel="stylesheet">
<script src="{{ asset( theme('admin.assets', 'js/bootstrap-select/bootstrap-select.min.js') ) }}"></script>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->


@push('jquery-scripts')
$('select').selectpicker();
@endpush