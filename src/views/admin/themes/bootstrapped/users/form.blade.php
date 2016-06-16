@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="active">
            <i class="fa fa-dashboard"></i> <a href="{{ route('admin.dashboard') }}">{{ trans('admin.dashboard.header') }}</a>
        </li>
        <li class="active">
            <a href="{{ route('admin.users.index') }}">{{ trans('admin.user.breadcrumb') }}</a>
        </li>
        <li class="active">
            {{ $header }}
        </li>
    </ol>
@endsection

<h1 class="page-header">
    @if(isset($model))
    {{ $model->name }}
    @else
    New User
    @endif
</h1>

<div class="row">
    <div class="col-lg-7">
        @if(isset($model))
        {!! Form::model($model, [
            'class' => 'form-default',
            'method' => empty($disabled) ? 'PUT' : 'PATCH',
            'files' => true,
            'route' => [
                'admin.users.update', $model->id
            ]
        ]) !!}
        @else
        {!! Form::open([
            'class' => 'form-default',
            'files' => true,
            'route' => 'admin.users.store'
        ]) !!}
        @endif
            <div class="form-group">
                {!! Form::label('name', trans('admin.user.form.label.name')) !!}
                {!! Form::text('name', null, ['class' => 'form-control', $disabled]) !!}
                {!! $errors->first('name', '<div class="text-danger">:message</div>') !!}
            </div>
            <div class="form-group">
                {!! Form::label('email', trans('admin.user.form.label.email')) !!}
                {!! Form::email('email', null, ['class' => 'form-control', $disabled]) !!}
                {!! $errors->first('email', '<div class="text-danger">:message</div>') !!}
            </div>
            <div class="form-group">
                {!! Form::label('password', trans('admin.user.form.label.password')) !!}
                {!! Form::password('password', ['class' => 'form-control', $disabled]) !!}
                {!! $errors->first('password', '<div class="text-danger">:message</div>') !!}
            </div>
            <div class="form-group">
                {!! Form::label('role', trans('admin.user.form.label.role')) !!}
                {!! Form::select('role', $roles, isset($role) ? $role : null, ['class' => 'form-control', $disabled]) !!}
            </div>
            @if(!isset($model) or auth()->user()->id <> $model->id)
            <div class="form-group">
                {!! Form::label('published', trans('articles::articles.admin.form.label.published')) !!}
                <div class="switch mini">
                    <input name="published" id="published" {{ (@$model->published?'checked':'') }} {{ $disabled }} value="1" type="checkbox" />
                    <label for="published"></label>
                    <i class="active">Enabled</i>
                </div>
            </div>
            @endif

            <div class="form-group margin-20x-top">
                @if(empty($disabled))
                {!! Form::submit( trans('admin.user.form.button.' . (isset($model) ? 'update' : 'save')), [
                    'class' => 'btn btn-lg btn-primary form-button',
                    'role' => 'form-button'
                ]) !!}
                @else
                {!! Form::submit( trans('admin.user.form.button.restore'), [
                    'class' => 'btn btn-lg btn-primary form-button',
                    'role' => 'form-button'
                ]) !!}
                @endif
                <a href="{!! route('admin.users.index') !!}" class="btn" role="form-button simple">
                    @if(isset($model))
                    {{ trans('admin.user.form.button.close') }}
                    @else
                    {{ trans('admin.user.form.button.cancel') }}
                    @endif
                </a>
            </div>

        {!! Form::close() !!}
    </div>
</div>


@push('jquery-scripts')
    $('form').on('submit', function() {
        App.set('page_exit_confirmation', false);
        return true;
    });
    $('input,select').on('change', function() {
        App.set('page_exit_confirmation', true);
    });
@endpush