var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass('styles.scss');
    mix.scripts('image-manager.js', 'public/js/image-manager.js');
    mix.scripts('book-sidebar.js', 'public/js/book-sidebar.js');
    mix.scripts('jquery-extensions.js', 'public/js/jquery-extensions.js');
});
