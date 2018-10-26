'use strict';

var gulp = require('gulp'),
    autoprefixer = require('gulp-autoprefixer'),
    combineMq = require('gulp-combine-mq'),
    concat = require('gulp-concat'),
    htmlreplace = require('gulp-html-replace'),
    jshint = require('gulp-jshint'),
    livereload = require('gulp-livereload'),
    rename = require('gulp-rename'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    modernizr = require('gulp-modernizr'),
    notifier = require('node-notifier'),
    browserify = require('browserify'),
    source = require('vinyl-source-stream');

// SASS DEV
gulp.task('sass', function () {
    return gulp.src('./scss/main.scss')
        .pipe(sass({outputStyle: 'expanded'}).on('error', sass.logError))
        .pipe(autoprefixer('last 2 version', 'ie 9'))
        .pipe(gulp.dest('./css'));
});

// JS DEV
gulp.task('scripts', function() {
    return browserify('./scripts/main.js')
        .bundle()
        .pipe(source('main.js'))
        .pipe(gulp.dest('./js/'));
});

// SASS BUILD
gulp.task('sassmin', function () {
    return gulp.src('./scss/main.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(combineMq({beautify: false}))
        .pipe(autoprefixer('last 2 version', 'ie 9'))
        .pipe(gulp.dest('./css'))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./css'));
});

// JS BUILD
gulp.task('scriptsmin', function() {
    return gulp.src(['./scripts/plugins/*.js','./scripts/main.js'])
        .pipe(browserify({
        insertGlobals : true,
        debug : !gulp.env.production
        }))
        .pipe(gulp.dest('./build/js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('./js/'));
});

// MODERNIZR
gulp.task('modernizr', function() {
    return gulp.src(['./js/main.js'],['./css/main.min.css'])
        .pipe(modernizr({
            excludeTests: [''],
            options: ['setClasses'],
            tests: ['csstransforms3d', 'flexbox', 'flexboxlegacy', 'flexwrap', 'touchevents', 'pointerevents']
        }))
        .pipe(rename('./modernizr.js'))
        .pipe(gulp.dest('./js/plugins/'));
});


// CREATE A RANDOM VERSION NUMBER
function makeid()
{
    var text = "";
    var possible = "0123456789";

    for( var i=0; i < 10; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    return text;
}

// CHANGE CSS AND JS SRC BETWEEN DEV AND PRODUCTION, ADD/REMOVE LIVERELOAD
var liveReloadString = "<script>document.write('<script src=\"http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1\"></' + 'script>')</script>";

gulp.task('cssJsProduction', function() {
    gulp.src('./templates/layouts/**/*.twig')
        .pipe(htmlreplace({
            css: {
                src: '{{site.theme.link}}/css/main.min.css?' + makeid(),
                tpl: '<link rel="stylesheet" href="%s">'
            },
            js: {
                src: '{{site.theme.link}}/js/scripts.min.js?' + makeid(),
                tpl: '<script src="%s"></script>'
            },
            liveReload: {
                src: '',
                tpl: '%s'
            }
        }, {keepBlockTags: true}))
        .pipe(gulp.dest('./templates/'));
});

gulp.task('cssJsDevelopment', function() {
    gulp.src('./templates/layouts/**/*.twig')
        .pipe(htmlreplace({
            css: {
                src: '{{site.theme.link}}/css/main.css',
                tpl: '<link rel="stylesheet" href="%s">'
            },
            js: {
                src: '{{site.theme.link}}/js/scripts.js',
                tpl: '<script src="%s"></script>'
            },
            liveReload: {
                src: liveReloadString,
                tpl: '%s'
            }
        }, {keepBlockTags: true}))
        .pipe(gulp.dest('./templates/'));
});



// TASKS
gulp.task('default', ['sassmin', 'modernizr', 'scriptsmin', 'cssJsProduction'], function() {
    notifier.notify({ title: 'Production Build', message: 'Done' });
});
gulp.task('build', ['sassmin', 'modernizr', 'scriptsmin', 'cssJsProduction'], function() {
    notifier.notify({ title: 'Production Build', message: 'Done' });
});
gulp.task('dev', ['sass', 'scripts', 'cssJsDevelopment'], function() {
    notifier.notify({ title: 'Development Build', message: 'Done' });
});
 

// WATCH DEV + LIVERELOAD
gulp.task('watch', function() {

    // Watch .scss files
    gulp.watch('./scss/**', ['sass', 'cssJsDevelopment']);

    // Watch .js files
    gulp.watch('./scripts/main.js', ['scripts', 'cssJsDevelopment']);

    // Create LiveReload server
    livereload.listen();

    // Watch any changed files, reload on change
    gulp.watch(['./css/*','./js/*','./templates/**']).on('change', livereload.changed);
});


// WATCH BUILD
gulp.task('watchbuild', function() {

    // Watch .scss files
    gulp.watch('./scss/**', ['sassmin', 'cssJsProduction']);

    // Watch .js files
    gulp.watch('./scripts/main.js', ['scriptsmin', 'cssJsProduction']);

    // Watch any changed files, reload on change
    gulp.watch(['./css/*','./js/*','./templates/**']);
});
