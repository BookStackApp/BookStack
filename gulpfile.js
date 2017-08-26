'use strict';

const argv = require('yargs').argv;
const gulp = require('gulp'),
    plumber = require('gulp-plumber');

const autoprefixer = require('gulp-autoprefixer');
const minifycss = require('gulp-clean-css');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');

const browserify = require("browserify");
const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
const babelify = require("babelify");
const watchify = require("watchify");
const envify = require("envify");
const uglify = require('gulp-uglify');

const gutil = require("gulp-util");
const liveReload = require('gulp-livereload');

if (argv.production) process.env.NODE_ENV = 'production';
let isProduction = argv.production || process.env.NODE_ENV === 'production';

gulp.task('styles', () => {
    let chain = gulp.src(['resources/assets/sass/**/*.scss'])
        .pipe(sourcemaps.init())
        .pipe(plumber({
            errorHandler: function (error) {
                console.log(error.message);
                this.emit('end');
            }}))
        .pipe(sass())
        .pipe(autoprefixer('last 2 versions'));
    if (isProduction) chain = chain.pipe(minifycss());
    chain = chain.pipe(sourcemaps.write());
    return chain.pipe(gulp.dest('public/css/')).pipe(liveReload());
});


function scriptTask(watch = false) {

    let props = {
        basedir: 'resources/assets/js',
        debug: true,
        entries: ['global.js'],
        fast: !isProduction,
        cache: {},
        packageCache: {},
    };

    let bundler = watch ? watchify(browserify(props), { poll: true }) : browserify(props);

    if (isProduction) {
        bundler.transform(envify, {global: true}).transform(babelify, {presets: ['es2015']});
    }

    function rebundle() {
        let stream = bundler.bundle();
        stream = stream.pipe(source('common.js'));
        if (isProduction) stream = stream.pipe(buffer()).pipe(uglify());
        return stream.pipe(gulp.dest('public/js/')).pipe(liveReload());
    }

    bundler.on('update', function() {
        rebundle();
        gutil.log('Rebundling assets...');
    });

    bundler.on('log', gutil.log);
    return rebundle();
}

gulp.task('scripts', () => {scriptTask(false)});
gulp.task('scripts-watch', () => {scriptTask(true)});

gulp.task('default', ['styles', 'scripts-watch'], () => {
    liveReload.listen();
    gulp.watch("resources/assets/sass/**/*.scss", ['styles']);
});

gulp.task('build', ['styles', 'scripts']);