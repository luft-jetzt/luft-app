let gulp = require('gulp');
let minify = require('gulp-minify');
let cleanCSS = require('gulp-clean-css');
let concat = require('gulp-concat');
let urlAdjuster = require('gulp-css-replace-url');

let sass = require('gulp-sass');
sass.compiler = require('node-sass');

gulp.task('leaflet-images', function () {
    return gulp.src('node_modules/leaflet/dist/images/*')
        .pipe(gulp.dest('public/img/leaflet'));
});

gulp.task('leaflet-css', [], function() {
    return gulp.src('node_modules/leaflet/dist/leaflet.css')
        .pipe(urlAdjuster({
            replace: ['images/','/img/leaflet/'],
        }))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('build-leaflet', ['leaflet-images', 'leaflet-css']);

gulp.task('extramarkers-images', function () {
    return gulp.src('node_modules/leaflet-extra-markers/dist/img/*')
        .pipe(gulp.dest('public/img/leaflet-extra-markers'));
});

gulp.task('extramarkers-css', [], function() {
    return gulp.src('node_modules/leaflet-extra-markers/dist/css/leaflet.extra-markers.min.css')
        .pipe(urlAdjuster({
            replace: ['../img/','/img/leaflet-extra-markers/'],
        }))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('build-extramarkers', ['extramarkers-images', 'extramarkers-css']);

gulp.task('sass', function () {
    return gulp.src('assets/scss/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('copy-asset-images', function () {
    return gulp.src('assets/img/*/*')
        .pipe(gulp.dest('public/img/'));
});

gulp.task('copy-fonts', function () {
    return gulp.src('node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest('public/fonts'));
});

gulp.task('compress-css', ['leaflet-css', 'sass'], function () {
    return gulp.src([
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'node_modules/font-awesome/css/font-awesome.css',
            'assets/css/*',
        ])
        .pipe(cleanCSS())
        .pipe(concat('luft.min.css'))
        .pipe(gulp.dest('public/css/'));
});

gulp.task('compress-js', function () {
    return gulp.src([
            'node_modules/jquery/dist/jquery.min.js',
            'node_modules/popper.js/dist/popper.js',
            'node_modules/bootstrap/dist/js/bootstrap.min.js',
            'node_modules/leaflet/dist/leaflet.js',
            'node_modules/leaflet-extra-markers/dist/js/leaflet.extra-markers.min.js',
            'node_modules/typeahead.js/dist/bloodhound.min.js',
            'node_modules/typeahead.js/dist/typeahead.jquery.min.js',
            'assets/js/*',
        ])
        .pipe(minify())
        .pipe(gulp.dest('public/js/'));
});

gulp.task('build', ['build-leaflet', 'build-extramarkers', 'copy-asset-images', 'copy-fonts', 'compress-js', 'compress-css'], function () {});

gulp.task('default', ['build']);
