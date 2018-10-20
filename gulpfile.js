let gulp = require('gulp');
let minify = require('gulp-minify');
let cleanCSS = require('gulp-clean-css');
let concat = require('gulp-concat');
let urlAdjuster = require('gulp-css-replace-url');

let sass = require('gulp-sass');
sass.compiler = require('node-sass');

gulp.task('sass', function () {
    return gulp.src('assets/scss/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('copy-images', function () {
    return gulp.src('node_modules/leaflet/dist/images/*')
        .pipe(gulp.dest('public/img/leaflet/'));
});

gulp.task('copy-asset-images', function () {
    return gulp.src('assets/img/*/*')
        .pipe(gulp.dest('public/img/'));
});

gulp.task('copy-fonts', function () {
    return gulp.src('node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest('public/fonts'));
});

gulp.task('compress-css', ['sass'], function () {
    return gulp.src([
            'assets/css/*',
            'node_modules/leaflet/dist/leaflet.css',
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'node_modules/font-awesome/css/font-awesome.css',
        ])
        .pipe(cleanCSS())
        .pipe(urlAdjuster({
            replace: ['images/','/img/leaflet/'],
        }))
        .pipe(concat('luft.min.css'))
        .pipe(gulp.dest('public/css/'));
});

gulp.task('compress-js', function () {
    return gulp.src([
            'node_modules/jquery/dist/jquery.slim.js',
            'node_modules/popper.js/dist/popper.js',
            'node_modules/bootstrap/dist/js/bootstrap.min.js',
            'node_modules/leaflet/dist/leaflet.js',
            'assets/js/*',
        ])
        .pipe(minify())
        .pipe(gulp.dest('public/js/'));
});

gulp.task('build', ['copy-images', 'copy-asset-images', 'copy-fonts', 'compress-js', 'compress-css'], function () {});

gulp.task('default', ['build']);
