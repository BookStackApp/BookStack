var elixir = require('laravel-elixir');


elixir(function(mix) {
    mix.sass('styles.scss');
    mix.scripts('image-manager.js', 'public/js/image-manager.js');
    mix.browserify(['jquery-extensions.js', 'pages/book-show.js' ,'global.js'], 'public/js/common.js');
});
