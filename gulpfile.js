var elixir = require('laravel-elixir');

elixir.config.js.browserify.transformers.push({
    name: 'vueify',
    options: {}
});

elixir(function(mix) {
    mix.sass('styles.scss')
        .sass('print-styles.scss')
        .browserify(['jquery-extensions.js', 'global.js'], 'public/js/common.js')
        .version(['css/styles.css', 'css/print-styles.css', 'js/common.js']);
});
