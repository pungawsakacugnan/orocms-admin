var elixir = require('laravel-elixir');

var paths = {
    'assets': {
        'admin': './resources/assets/admin/themes/bootstrapped/'
    },
    'bower_path': './bower_components/',
    'fontawesome': './bower_components/fontawesome/',

    'dest': './public/assets/admin/themes/bootstrapped/'
}

// backend
elixir(function(mix) {
    mix
        // compile sass file
        .sass('../admin/themes/bootstrapped/sass/app.scss', paths.dest + 'css/', {
            includePaths: [
                paths.fontawesome + 'scss/',
                paths.assets.admin + 'sass/'
            ]
        })

        // combine scripts
        .scripts([
            paths.bower_path + 'jquery/dist/jquery.min.js',
            paths.bower_path + 'history.js/scripts/bundled/html4+html5/jquery.history.js',
            paths.bower_path + 'bootstrap/dist/js/bootstrap.min.js',
            paths.bower_path + 'bootstrap-table/dist/bootstrap-table.min.js',
            paths.bower_path + 'bootstrap-table/dist/extensions/cookie/bootstrap-table-cookie.min.js',
            paths.bower_path + 'bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min.js',
            paths.bower_path + 'bootbox/bootbox.js',
            paths.bower_path + 'nprogress/nprogress.js',

            // datetimepicker
            paths.bower_path + 'moment/min/moment-with-locales.min.js',
            paths.bower_path + 'moment-timezone/builds/moment-timezone-with-data.min.js',
            paths.bower_path + 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',

            paths.assets.admin + 'js/common.js',
            paths.assets.admin + 'js/app.js',
        ], paths.dest + 'js/app.js', './')

        // combine styling
        .styles([
            paths.assets.admin + 'css/dashicons.css',
            paths.assets.admin + 'css/roboto.css',
            paths.fontawesome + 'css/font-awesome.min.css',
            paths.bower_path + 'bootstrap/dist/css/bootstrap.min.css',
            paths.bower_path + 'bootstrap-table/dist/bootstrap-table.min.css',

            // datetimepicker
            paths.bower_path + 'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',

            paths.bower_path + 'nprogress/nprogress.css',
            paths.dest + 'css/app.css'
        ], paths.dest + 'css/app.css', './')

        // fonts
        .copy(paths.assets.admin + 'fonts/**', 'public/build/assets/admin/themes/bootstrapped/fonts')
        .copy(paths.bower_path + 'bootstrap/fonts/**', 'public/build/assets/admin/themes/bootstrapped/fonts')
        .copy(paths.fontawesome + 'fonts/**', 'public/build/assets/admin/themes/bootstrapped/fonts')

        // boostrap-group-select
        .copy(paths.bower_path + 'bootstrap-group-select/src/bootstrap-group-select.js', paths.dest + 'js/bootstrap-group-select/bootstrap-group-select.min.js')

        // select2
        .copy(paths.bower_path + 'select2/select2.min.js', paths.dest + 'js/select2/select2.min.js')
        .copy(paths.bower_path + 'select2/select2.png', paths.dest + 'js/select2/select2.png')
        .copy(paths.bower_path + 'select2/select2-spinner.gif', paths.dest + 'js/select2/select2-spinner.gif')
        .copy(paths.bower_path + 'select2/select2x2.png', paths.dest + 'js/select2/select2x2.png')
        .styles([
            paths.bower_path + 'select2/select2.css',
            paths.bower_path + 'select2-bootstrap-css/select2-bootstrap.min.css'
        ], paths.dest + 'js/select2/select2.css', './')

        // boostrap-group-select
        .copy(paths.bower_path + 'bootstrap-select/dist/css/bootstrap-select.min.css', paths.dest + 'js/bootstrap-select/bootstrap-select.min.css')
        .copy(paths.bower_path + 'bootstrap-select/dist/js/bootstrap-select.min.js', paths.dest + 'js/bootstrap-select/bootstrap-select.min.js')

        // versioning
        .version(['assets/admin/themes/bootstrapped/css/app.css', 'assets/admin/themes/bootstrapped/js/app.js']);
});
