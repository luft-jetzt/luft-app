let gulp = require('gulp');
let minify = require('gulp-minify');
let cleanCSS = require('gulp-clean-css');
let concat = require('gulp-concat');

gulp.task('copy-images', function () {
    return gulp.src('node_modules/leaflet/dist/images/*')
        .pipe(gulp.dest('public/img/leaflet/'));
});

gulp.task('copy-asset-images', function () {
    return gulp.src('assets/img/*/*')
        .pipe(gulp.dest('public/img/'));
});

gulp.task('compress-css', function () {
    return gulp.src([
            'node_modules/leaflet/dist/leaflet.css',
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'assets/css/*/*',
        ])
        .pipe(cleanCSS())
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

gulp.task('build', ['copy-images', 'copy-asset-images', 'compress-js', 'compress-css'], function () {});

gulp.task('default', ['build']);
