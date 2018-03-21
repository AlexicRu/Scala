var gulp = require('gulp'),
    less = require('gulp-less'),
    csso = require('gulp-csso'),
    modifyCssUrls = require('gulp-modify-css-urls'),
    svgMin = require('gulp-svgmin'),
    pngquant = require('gulp-pngquant'),
    uglify = require('gulp-uglify')
;

var
    buildTime = + new Date(),
    srcPath = 'assets/',
    buildPath = 'assets/build/'
;

gulp.task('css', function(){
    return gulp.src(srcPath + 'css/**/*.less')
        .pipe(less())
        .pipe(modifyCssUrls({
            modify: function (url, filePath) {
                /*if (url.includes('../img/')) {
                    url = '../' + url;
                }*/
                if (buildTime) {
                    if (url.includes('?')) {
                        url += '&t=' + buildTime;
                    } else {
                        url += '?t=' + buildTime;
                    }
                }
                return url;
            }
        }))
        .pipe(csso())
        .pipe(gulp.dest(buildPath + 'css'))
});

gulp.task('js', function(){
    return gulp.src(srcPath + 'js/**/*.js')
        .pipe(uglify())
        .pipe(gulp.dest(buildPath + 'js'))
});

gulp.task('fonts', function() {
    return gulp.src(srcPath + 'fonts/**/*.*')
        .pipe(gulp.dest(buildPath + 'fonts'))
});

gulp.task('gif', function() {
    return gulp.src(srcPath + 'img/**/*.gif')
        .pipe(gulp.dest(buildPath + 'img'))
});

gulp.task('svg', function() {
    return gulp.src(srcPath + 'img/**/*.svg')
        .pipe(svgMin())
        .pipe(gulp.dest(buildPath + 'img'));
});

gulp.task('png', function() {
    return gulp.src(srcPath + 'img/**/*.png')
        .pipe(pngquant({
            quality: '100'
        }))
        .pipe(gulp.dest(buildPath + 'img'));
});

gulp.task('watch', ['build'], function() {
    gulp.watch(srcPath + 'js/**/*.js', ['js']);
    gulp.watch(srcPath + 'css/**/*.less', ['css']);
    gulp.watch(srcPath + 'img/**/*.svg', ['svg']);
    gulp.watch(srcPath + 'img/**/*.png', ['png']);
    gulp.watch(srcPath + 'img/**/*.gif', ['gif']);
});

gulp.task('build', ['css', 'js', 'fonts', 'svg', 'png', 'gif']);
gulp.task('fast', ['css', 'js']);
gulp.task('default', ['watch']);