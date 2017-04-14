const argv = require('yargs').argv;
const gulp = require('gulp'),
    plumber = require('gulp-plumber');
const autoprefixer = require('gulp-autoprefixer');
const uglify = require('gulp-uglify');
const minifycss = require('gulp-clean-css');
const sass = require('gulp-sass');
const browserify = require("browserify");
const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
const babelify = require("babelify");
const watchify = require("watchify");
const envify = require("envify");
const gutil = require("gulp-util");

if (argv.production) process.env.NODE_ENV = 'production';

gulp.task('styles', () => {
    let chain = gulp.src(['resources/assets/sass/**/*.scss'])
        .pipe(plumber({
            errorHandler: function (error) {
                console.log(error.message);
                this.emit('end');
            }}))
        .pipe(sass())
        .pipe(autoprefixer('last 2 versions'));
    if (argv.production) chain = chain.pipe(minifycss());
    return chain.pipe(gulp.dest('public/css/'));
});


function scriptTask(watch=false) {

    let props = {
        basedir: 'resources/assets/js',
        debug: true,
        entries: ['global.js']
    };

    let bundler = watch ? watchify(browserify(props), { poll: true }) : browserify(props);
    bundler.transform(envify, {global: true}).transform(babelify, {presets: ['es2015']});
    function rebundle() {
        let stream = bundler.bundle();
        stream = stream.pipe(source('common.js'));
        if (argv.production) stream = stream.pipe(buffer()).pipe(uglify());
        return stream.pipe(gulp.dest('public/js/'));
    }
    bundler.on('update', function() {
        rebundle();
        gutil.log('Rebundle...');
    });
    bundler.on('log', gutil.log);
    return rebundle();
}

gulp.task('scripts', () => {scriptTask(false)});
gulp.task('scripts-watch', () => {scriptTask(true)});

gulp.task('default', ['styles', 'scripts-watch'], () => {
    gulp.watch("resources/assets/sass/**/*.scss", ['styles']);
});

gulp.task('build', ['styles', 'scripts']);