var gulp       = require('gulp')
    watch      = require('gulp-watch'),
    sass       = require('gulp-sass'),
    less       = require('gulp-less'),
    coffee     = require('gulp-coffee'),
    concat     = require('gulp-concat'),
    uglify     = require('gulp-uglify'),
    addsrc     = require('gulp-add-src'),
    sourcemaps = require('gulp-sourcemaps');

// var beSassy = function() {
//     console.log('Being Sassy');

//     gulp.src([
//         './scss/common.scss',
//         './scss/dreamer.scss',
//         './scss/admin.scss'
//     ])
//     .pipe(sourcemaps.init())
//     .pipe(sass({outputStyle: 'compressed', includePaths: sassPaths}).on('error', sass.logError))
//     .pipe(sourcemaps.write('./'))
//     .pipe(gulp.dest('../assets/css'));
// }

var lessIsMore = function() {
    var lessConfig = {
        compress: true,
        paths: ['node_modules']
    };

    gulp.src(['./less/traq.less'])
        .pipe(less(lessConfig))
        .pipe(sourcemaps.init())
        .pipe(addsrc('node_modules/simplemde/dist/simplemde.min.css'))
        .pipe(concat('traq.css'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('../assets/css'));
}

var makeCoffee = function() {
    console.log('Making coffee');

    gulp.src('coffee/*.coffee')
        .pipe(coffee())
        .pipe(concat('traq.js'))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('../assets/js'));
}

gulp.task('compile', function(){
    // beSassy();
    lessIsMore();
    makeCoffee();
    gulp.start('assets');
});

// Watch for changes
gulp.task('watch', function(){
    // watch('scss/**/*.scss', beSassy);
    watch('less/**/*.less', lessIsMore);
    watch('coffee/**/*.coffee', makeCoffee);

    // gulp.start('assets');
});

// Compile Sass
// gulp.task('sass', beSassy);

// Compile Less
gulp.task('less', lessIsMore);

// Compile CoffeeScript
gulp.task('coffee', makeCoffee);

// JavaScripts
gulp.task('assets', function() {
    gulp.src([
        'node_modules/jquery/dist/jquery.js',
        'node_modules/js-cookie/src/js.cookie.js',
        'node_modules/chosen-npm/public/chosen.jquery.js',
        'node_modules/bootstrap/dist/js/bootstrap.js',
        'node_modules/moment/min/moment-with-locales.js',
        // 'node_modules/moment-timezone/builds/moment-timezone-with-data.js',
        'node_modules/eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
        'node_modules/simplemde/dist/simplemde.min.js'
    ])
    .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(concat('js.js'))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('../assets/js'));

    gulp.src(['node_modules/font-awesome/fonts/*'])
        .pipe(gulp.dest('../assets/fonts'));

    gulp.src([
        'node_modules/bootstrap-chosen/chosen-sprite.png',
        'node_modules/bootstrap-chosen/chosen-sprite@2x.png'
    ])
    .pipe(gulp.dest('../assets/img'));
});
