var elixir = require('laravel-elixir');

// Custom extensions
var gulp = require('gulp');
var Task = elixir.Task;
var fs = require('fs');

elixir.extend('queryVersion', function(inputFiles) {
     new Task('queryVersion', function() {
         var manifestObject = {};
         var uidString = Date.now().toString(16).slice(4);
         for (var i = 0; i < inputFiles.length; i++) {
             var file = inputFiles[i];
             manifestObject[file] = file + '?version=' + uidString;
         }
         var fileContents = JSON.stringify(manifestObject, null, 1);
         fs.writeFileSync('public/build/manifest.json', fileContents);
     }).watch(['./public/css/*.css', './public/js/*.js']);
});

elixir(function(mix) {
    mix.sass('styles.scss')
        .sass('print-styles.scss')
        .browserify('global.js', 'public/js/common.js')
        .queryVersion(['css/styles.css', 'css/print-styles.css', 'js/common.js']);
});
