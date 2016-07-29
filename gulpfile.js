var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var less = require('gulp-less');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var env = process.env.GULP_ENV;

gulp.task('js', function () {
    return gulp.src(['bower_components/jquery/dist/jquery.js',
            'bower_components/bootstrap/dist/js/bootstrap.js',
            'app/Resources/public/js/**/*.js'])
        .pipe(concat('javascript.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/js'));
});

gulp.task('css', function () {
    return gulp.src([
            'bower_components/bootstrap/dist/css/bootstrap.css',
            'app/Resources/public/less/**/*.less'])
        .pipe(gulpif(/[.]less/, less()))
        .pipe(concat('styles.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/css'));
});

gulp.task('img', function() {
    return gulp.src('app/Resources/public/img/**/*.*')
        .pipe(gulp.dest('web/img'));
});

gulp.task('default', ['js', 'css', 'img']);