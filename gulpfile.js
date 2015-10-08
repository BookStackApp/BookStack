var elixir = require('laravel-elixir');

elixir.config.js.browserify.transformers.push({
    name: 'vueify',
    options: {}
});

elixir(function(mix) {
    mix.sass('styles.scss');
    mix.browserify(['jquery-extensions.js', 'global.js'], 'public/js/common.js');
});
