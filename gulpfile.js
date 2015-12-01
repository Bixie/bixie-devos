var pkg     = require('./composer.json'),
    date    = require('date-utils'),
    del     = require('del'),
    fs      = require('fs'),
    gulp    = require('gulp'),
    less    = require('gulp-less'),
    minify  = require('gulp-minify-css'),
    replace = require('gulp-replace'),
    run     = require('gulp-run'),
    sym     = require('gulp-sym'),
    uglify  = require('gulp-uglify'),
    util    = require('gulp-util'),
    zip     = require('gulp-zip'),
    jshint  = require('gulp-jshint'),
    stylish = require('jshint-stylish'),
    merge   = require('merge-stream'),
    request = require('request'),
    runSeq  = require('run-sequence'),
    xml2js  = require('xml2js');



var files  = [
        'admin/assets/**/*',
        'admin/languages/**/*',
        'admin/plugins/**/*',
        'admin/src/**/*',
        'admin/views/**/*',
        'admin/vendor/assets/gmaps/{lib/**,*.js,*.json,*.md}',
        'admin/vendor/assets/uikit/**/{font*,Font*,*.less,*.min.js}',
        'admin/vendor/abeautifulsite/*/{src/**,*.json,*.md}',
        'admin/vendor/jublonet/*/{src/**,*.json,*.md}',
        'admin/vendor/{composer/**,yootheme/**,autoload*}',
        '{bower.json,composer.json,config.php,*.md}'
    ],

    output = util.env.output || util.env.o || '_build',
    system = __dirname.match(/wp-content/) ? 'wordpress' : 'joomla',
    parser = new xml2js.Parser({ async: false });

/* core */

gulp.task('clean', function (cb) {
    del(['_build/dist', '_build/_temp'], cb);
});

gulp.task('lint', function () {
    return gulp.src(['assets/js/*.js', 'plugins/**/*.js'])
        .pipe(jshint())
        .pipe(jshint.reporter(stylish));
});

gulp.task('compile', function () {
    return gulp.src('assets/less/*.less').pipe(less()).pipe(gulp.dest('assets/css'));
});

gulp.task('prepare', function () {
    return merge(
        // fix autoload namespace
        gulp.src('_build/dist/**/vendor/composer/*.php').pipe(replace('Composer\\Autoload', 'YOOtheme\\Autoload')),

        // compress
        gulp.src(['_build/dist/**/assets/**/*.css', '!**/vendor/**']).pipe(minify()),
        gulp.src(['_build/dist/**/assets/**/*.js', '_build/dist/**/plugins/**/*.js', '!**/vendor/**']).pipe(uglify()),

        // remove sourceMappingURL
        gulp.src('_build/dist/**/assets/**/*min.js').pipe(replace(/# sourceMappingURL=[^\s]+/g, ''))
    ).pipe(gulp.dest('_build/dist/'));
});

gulp.task('build', ['translate'], function () {
    runSeq('build-wordpress', 'build-joomla');
});

gulp.task('default', ['compile']);

/* install */

gulp.task('install', function (cb) {
    run('composer update && bower update').exec('', function () {
        runSeq('install-' + system, cb);
    });
});

gulp.task('install-wordpress', ['compile'], function () {
    return gulp.src(['_build/wordpress/**/*']).pipe(gulp.dest('./'));
});

gulp.task('install-joomla', ['compile'], function () {
    return merge(
        gulp.src(['_build/joomla/packages/com_widgetkit/widgetkit.xml', '_build/joomla/packages/com_widgetkit/admin/*', '!**/language']).pipe(gulp.dest('./')),
        gulp.src([
            '_build/joomla/packages/mod_widgetkit/',
            '_build/joomla/packages/plg_content_widgetkit/',
            '_build/joomla/packages/plg_extd_widgetkit/',
            '_build/joomla/packages/plg_system_widgetkit_k2/',
            '_build/joomla/packages/plg_system_widgetkit_zoo/'
        ]).pipe(sym([
            '../../../modules/mod_widgetkit/',
            '../../../plugins/content/widgetkit/',
            '../../../plugins/editors-xtd/widgetkit/',
            '../../../plugins/system/widgetkit_k2/',
            '../../../plugins/system/widgetkit_zoo/'
        ], { force: true }))
    );
});

/* wordpress */

gulp.task('copy-wordpress', function () {
    return merge(
        gulp.src(['!**/joomla/**/*', '!**/*joomla*'].concat(files), { base: './' }).pipe(gulp.dest('_build/dist/widgetkit')),
        gulp.src('_build/wordpress/**/*', { base: './_build/wordpress' }).pipe(gulp.dest('_build/dist/widgetkit')),
        gulp.src(['_build/_temp/widgetkit-language-master/*', '!*en_GB.json']).pipe(gulp.dest('_build/dist/widgetkit/languages'))
    );
});

gulp.task('prepare-wordpress', ['prepare'], function () {
    return tagHelper(gulp.src(['_build/dist/widgetkit/widgetkit.php', '_build/dist/widgetkit/config.php'])).pipe(gulp.dest('_build/dist/widgetkit/'));
});

gulp.task('zip-wordpress', function () {
    return gulp.src('_build/dist/**/*').pipe(zip('widgetkit_' + pkg.version + '_wp.zip')).pipe(gulp.dest(output));
});

gulp.task('build-wordpress', function (cb) {
    runSeq('translations', 'compile', 'copy-wordpress', 'prepare-wordpress', 'zip-wordpress', 'clean', cb);
});

/* joomla */

gulp.task('copy-joomla', function () {
    return merge(
        gulp.src(['!**/wordpress/**/*', '!**/*wordpress*', '!**/woocommerce/**/*'].concat(files), { base: './' }).pipe(gulp.dest('_build/dist/packages/com_widgetkit/admin')),
        gulp.src('_build/joomla/**/*', { base: './_build/joomla' }).pipe(gulp.dest('_build/dist')),
        gulp.src(['_build/_temp/widgetkit-language-master/*', '!*en_GB.json']).pipe(gulp.dest('_build/dist/packages/com_widgetkit/admin/languages'))
    );
});

gulp.task('prepare-joomla', ['prepare'], function () {
    return tagHelper(gulp.src(['_build/dist/**/*.xml', '_build/dist/packages/com_widgetkit/admin/config.php'], { base: '_build/dist' })).pipe(gulp.dest('_build/dist/'))
});

gulp.task('installer-joomla', function () {

    // make sure the destination folder exists
    if (!fs.existsSync('_build/dist/packages')) fs.mkdirSync('_build/dist/packages');

    return request
        .get('http://storage.googleapis.com/downloads-yootheme/installer/installer_yootheme_j33.zip')
        .on('error', function (err) {
            console.info(new Error('YOOtheme Joomla! Installer package download failed'));
        })
        .on('response', function (response) {
            if (response.statusCode != 200) console.info(new Error('YOOtheme Joomla! Installer package download failed'));
        })
        .pipe(fs.createWriteStream('_build/dist/packages/plg_installer_yootheme.zip'));
});

gulp.task('zip-packages-joomla', function () {
    return merge(
        gulp.src('_build/dist/packages/com_widgetkit/**/*').pipe(zip('com_widgetkit.zip')),
        gulp.src('_build/dist/packages/plg_content_widgetkit/**/*').pipe(zip('plg_content_widgetkit.zip')),
        gulp.src('_build/dist/packages/plg_system_widgetkit_zoo/**/*').pipe(zip('plg_system_widgetkit_zoo.zip')),
        gulp.src('_build/dist/packages/plg_system_widgetkit_k2/**/*').pipe(zip('plg_system_widgetkit_k2.zip')),
        gulp.src('_build/dist/packages/plg_extd_widgetkit/**/*').pipe(zip('plg_extd_widgetkit.zip')),
        gulp.src('_build/dist/packages/mod_widgetkit/**/*').pipe(zip('mod_widgetkit.zip'))
    ).pipe(gulp.dest('_build/dist/packages/'));
});

gulp.task('zip-joomla', ['zip-packages-joomla'], function () {
    return gulp.src(['_build/dist/**/*.zip', '_build/dist/pkg_widgetkit.xml', '_build/dist/script.php']).pipe(zip('widgetkit_' + pkg.version + '_j3.zip')).pipe(gulp.dest(output));
});

gulp.task('build-joomla', function (cb) {
    runSeq('translations', 'compile', 'copy-joomla', 'prepare-joomla', 'installer-joomla', 'zip-joomla', 'clean', cb);
});

/* helper */

function tagHelper(stream) {
    return stream.pipe(replace('{{VERSION}}', pkg.version))
        .pipe(replace('{{TITLE}}', pkg.title))
        .pipe(replace('{{DESCRIPTION}}', pkg.description))
        .pipe(replace('{{DATE}}', Date.today().toFormat('MMMM YYYY')))
        .pipe(replace('{{COPYRIGHT}}', pkg.copyright))
        .pipe(replace('{{LICENSE}}', pkg.license))
        .pipe(replace('{{AUTHOR}}', pkg.authors[0].name))
        .pipe(replace('{{AUTHOREMAIL}}', pkg.authors[0].email))
        .pipe(replace('{{AUTHORURL}}', pkg.authors[0].homepage))
        .pipe(replace("'{{DEBUG}}'", '0'));
}

/* translation */
gulp.task('translate', function () {
    var translations = {};
    return gulp
        .src('**/*.php')
        .pipe(replace(/(('|")((?:(?!\2).)+)\2\s*\|\s*trans)|(->trans(?:Choice)?\(('|")\s*((?:(?!\5).)+)\5\s*[),])/g, function (match, p1, p2, trans1, p3, p4, trans2) {
            translations[trans1 || trans2] = trans1 || trans2;
            return match;
        }))
        .on('end', function () {
            var trans = {};
            Object.keys(translations).sort().forEach(function (key) {
                trans[key] = translations[key]
            });
            fs.writeFile('languages/en_GB.json', JSON.stringify(trans, undefined, 4));
        });
});

gulp.task('translations', function () {

    var unzip = require('unzip');

    // make sure the destination folder exists
    if (!fs.existsSync('_build/_temp')) fs.mkdirSync('_build/_temp');

    return request
        .get('https://github.com/yootheme/widgetkit-language/archive/master.zip')
        .on('error', function (err) {
            console.info(new Error('Translations download failed'));
        })
        .on('response', function (response) {
            if (response.statusCode != 200) console.info(new Error('Translations download failed'));
        })
        .pipe(unzip.Extract({path: '_build/_temp'}));
});
