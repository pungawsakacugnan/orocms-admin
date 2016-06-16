<ul class="nav navbar-right top-nav">
    <li>
        <a href="/" title="Go to main site" target="_blank">
            <i class="glyphicon-fw glyphicon glyphicon-home" aria-hidden="true"></i></span> 
        </a>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            @if(auth()->check())
            <i class="glyphicon-fw glyphicon glyphicon-user" aria-hidden="true"></i> <span class="hidden-xs"> {{ auth()->user()->name }}</span> 
            @endif
            <i class="fa fa-fw fa-angle-down"></i>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="{{ route('admin.profile') }}"><i class="glyphicon-fw glyphicon glyphicon-user" aria-hidden="true"></i> {{ trans('admin.profile.edit') }}</a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.logout') }}"><i class="fa fa-fw fa-power-off"></i> {{ trans('admin.profile.logout') }}</a>
            </li>
        </ul>
    </li>
</ul>