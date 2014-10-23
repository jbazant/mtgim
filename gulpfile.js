// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var jshint = require('gulp-jshint');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');

// Lint Task
gulp.task('lint', function() {
    return gulp.src('web/public/js/src/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});


// Concatenate & Minify JS
gulp.task('scripts', function() {
    return gulp.src([
            'web/public/js/lib/jquery-1.11.0.min.js',
            'web/public/js/src/tracking.js',
            'web/public/js/src/*Page.js',
            'web/public/js/src/searchResult.js',
            'web/public/js/src/app.js',
            'web/public/js/src/init.js',
            'web/public/js/lib/jquery.mobile-1.4.2.min.js'
        ])
        .pipe(concat('app.js'))
        .pipe(gulp.dest('web/public/js/'))
        .pipe(rename('app.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('web/public/js/'));
});

gulp.task('styles', function() {
    return gulp.src([
            'web/public/css/jquery.mobile-1.4.2.min.css',
            'web/public/css/jb.min.css',
            'web/public/css/jquery.mobile.icons.min.css',
            'web/public/css/web.css'
        ])
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest('web/public/css/'))
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('web/public/js/src/*.js', ['scripts']);
    gulp.watch('web/public/css/*.css', ['styles']);
});

// Default Task
gulp.task('default', ['lint', 'scripts', 'styles', 'watch']);
gulp.task('compile', ['scripts', 'styles']);
