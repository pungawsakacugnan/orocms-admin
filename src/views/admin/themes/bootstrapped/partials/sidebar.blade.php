<div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav side-nav">
    @if (isset($menu_admin))
    @foreach($menu_admin->roots() as $item)
        @if($item->divider)
        <li role="separator" class="divider"></li>
        @endif
        <li role="child-li @if ($item->hasChildren()){{'parent'}} @endif" {!! $item->attributes() !!} @if($item->data('id')) data-id="{{ $item->data('id') }}" @endif >
            @if ($item->hasChildren())
            <a href="{{ $item->url() }}" data-toggle="collapse" data-target="#dd-menu-{{$item->id}}">
                @if($item->data('glyphicon'))<i class="{!! $item->data('glyphid') !!} {!! $item->data('glyphicon') !!}" aria-hidden="true"></i> @else {!! $item->icon !!}@endif <b>{!! $item->title !!}</b> <i class="pull-right fa fa-fw fa-angle-down"></i>
            </a>

            <ul id="dd-menu-{{$item->id}}" class="collapse @if($item->data('active')) in @endif">
                @foreach($item->children() as $child)
                @if($child->divider)
                <li role="separator" class="divider"></li>
                @endif
                <li class="@if($child->data('active')) active @endif" @if($child->data('id')) data-id="{{ $child->data('id') }}" @endif>
                    <a role="nav {{ md5($child->id) }}" href="{{ $child->url() }}"><b>{{ $child->title }}</b></a>
                </li>
                @endforeach
            </ul>
            @else
            <a role="nav {{ md5($item->id) }}" href="{{ $item->url() }}">@if($item->data('glyphicon'))<i class="{!! $item->data('glyphid') !!} {!! $item->data('glyphicon') !!}" aria-hidden="true"></i> @else {!! $item->icon !!}@endif <b>{{ $item->title }}</b></a>
            @endif
        </li>
    @endforeach
    @endif
    </ul>
</div>
