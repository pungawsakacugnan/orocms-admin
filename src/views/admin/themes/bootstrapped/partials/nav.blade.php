<nav class="navbar navbar-default navbar-ee navbar-fixed-top" role="navigation">
    <div class="navbar-header">
        <a class="navbar-brand" href="/admin">
            <span class="visible-xs">
                CP
            </span>
            <span class="visible-sm visible-md visible-lg" role="header-text">Control Panel</span>
        </a>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        @include('admin::partials.header')
    </div>

    @include('admin::partials.sidebar')
</nav>
