'use strict';

var babel = require('gulp-babel');
var browserify = require('browserify');
var buffer = require('vinyl-buffer');
var gulp = require('gulp');
var jasmine = require('gulp-jasmine');
var source = require('vinyl-source-stream');
var uglify = require('gulp-uglify');

gulp.task('browserify', ['babel-src'], () =>
   browserify({
         entries: ['./main.js'],
         basedir: './build/src',
         paths: './node_modules/react',
         debug: true
      })
      .bundle()
      .on('error', err => console.log('Error: ' + err.message))
      .pipe(source('main.js'))
      .pipe(buffer())
      .pipe(uglify())
      .pipe(gulp.dest('./public/')));

gulp.task('babel-src', () =>
   gulp.src('./assets/javascript/**/*.js')
      .pipe(babel())
      .pipe(gulp.dest('./build/src/')));

gulp.task('babel-test', () =>
   gulp.src('./tests/javascript/*.js')
      .pipe(babel())
      .pipe(gulp.dest('./build/test/')));

gulp.task('test', ['babel-src', 'babel-test'], () =>
   gulp.src('./build/test/*.js')
      .pipe(jasmine()));

gulp.task('prepare', ['test', 'browserify']);

gulp.task('default', ['browserify']);
