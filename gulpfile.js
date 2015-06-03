var gulp = require('gulp');
var sass = require('gulp-sass');

var config = {
    bootstrapDir: './vendor/bower/bootstrap-sass',
    publicDir: './web'
};

gulp.task('css', function() {
    return gulp.src('./static/scss/custom-bootstrap.scss')
        .pipe(sass({
            includePaths: [config.bootstrapDir + '/assets/stylesheets']
        }))
        .pipe(gulp.dest(config.publicDir + '/css'));
});

gulp.task('fonts', function() {
    return gulp.src(config.bootstrapDir + '/assets/fonts/**/*')
        .pipe(gulp.dest(config.publicDir + '/css/fonts'));
});

gulp.task('default', ['css', 'fonts']);

