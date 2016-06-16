@if(Session::has('flash_message'))
    <div class="alert flash-message alert-{!! Session::get('flash_type', 'info') !!} alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-ban"></i></button>
        {!! Session::get('flash_message') !!}
    </div>

    @push('jquery-scripts')
        $('.flash-message').delay(5000).fadeOut(function() {
            $(this).remove();
        });
    @endpush
@endif
