<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    @include('admin::partials.styles')
    @yield('style')
    <style>
        html, body {
            height: inherit;
        }
        body {
            background: #edeff0;
        }
    </style>
</head>
<body>
    <section>
        <div class="col-md-12 article">
            <form method="POST" class="form-signin" url="{{ route('admin.login.index') }}">
                {!! csrf_field() !!}
                <h2 class="form-signin-heading">Login</h2>

                <p>
                    Enter your email address and password to login.
                </p>

                @if(Session::has('flash_message'))
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-ban"></i></span></button>
                    <h4>{{ Session::get('flash_message') }}</h4>
                </div>
                @endif

                <label for="email" class="sr-only">Email address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="email address" required autofocus>
                <label for="password" class="sr-only">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="password" required>

                <br />
                <div class="form-group">
                    <div class="switch mini">
                        <input name="remember" id="remember" value="1" type="checkbox" />
                        <label for="remember"></label>
                        <i class="active">Keep me signed in</i>
                    </div>
                </div>

                <button class="btn btn-lg btn-primary" type="submit">Sign In</button>
            </form>
        </div>
    </section>
</body>
</html>
