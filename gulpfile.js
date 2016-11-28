var elixir = require('laravel-elixir');

elixir(mix => {
    mix.sass('styles.scss');
    mix.sass('print-styles.scss');
    mix.sass('export-styles.scss');
    mix.browserify('global.js', './public/js/common.js');
});
