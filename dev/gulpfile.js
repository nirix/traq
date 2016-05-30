var gulp         = require('gulp')
    less         = require('gulp-less'),
    coffee       = require('gulp-coffee'),
    concat       = require('gulp-concat'),
    uglify       = require('gulp-uglify'),
    addsrc       = require('gulp-add-src'),
    sourcemaps   = require('gulp-sourcemaps')
    autoprefixer = require('gulp-autoprefixer');

gulp.task('compile', function(){
    gulp.start('less');
    gulp.start('coffee');
    gulp.start('assets');
});

// Less
gulp.task('less', function(){
    var lessConfig = {
        compress: true,
        paths: ['node_modules']
    };

    gulp.src(['./less/traq.less'])
        .pipe(less(lessConfig))
        .pipe(autoprefixer({ browsers: ['last 2 versions'] }))
        .pipe(sourcemaps.init())
        .pipe(addsrc('node_modules/simplemde/dist/simplemde.min.css'))
        .pipe(concat('traq.css'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('../assets/css'));
});

// CoffeeScript
gulp.task('coffee', function(){
    gulp.src('coffee/*.coffee')
        .pipe(coffee())
        .pipe(concat('traq.js'))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('../assets/js'));
});

// Watch for changes
gulp.task('watch', function(){
    // watch('scss/**/*.scss', beSassy);
    // watch('less/**/*.less', lessIsMore);
    // watch('coffee/**/*.coffee', makeCoffee);

    gulp.watch('less/**/*.less', ['less']);
    gulp.watch('coffee/**/*.coffee', ['coffee']);

    // gulp.start('assets');
});

// JavaScripts
gulp.task('assets', function() {
    gulp.src([
        'node_modules/jquery/dist/jquery.js',
        // 'node_modules/js-cookie/src/js.cookie.js',
        // 'node_modules/chosen-npm/public/chosen.jquery.js',
        'node_modules/selectize/dist/js/standalone/selectize.js',
        'node_modules/bootstrap/dist/js/bootstrap.js',
        // 'node_modules/moment/min/moment-with-locales.js',
        // 'node_modules/moment-timezone/builds/moment-timezone-with-data.js',
        // 'node_modules/eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
        // 'node_modules/simplemde/dist/simplemde.min.js'
    ])
    .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(concat('js.js'))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('../assets/js'));

    gulp.src(['node_modules/font-awesome/fonts/*'])
        .pipe(gulp.dest('../assets/fonts'));

    // gulp.src([
    //     'node_modules/bootstrap-chosen/chosen-sprite.png',
    //     'node_modules/bootstrap-chosen/chosen-sprite@2x.png'
    // ])
    // .pipe(gulp.dest('../assets/img'));
});
