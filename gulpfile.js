var elixir = require('laravel-elixir');
require('laravel-elixir-livereload');

var themeDir = 'themes/octdemo/';
elixir.config.assetsPath = themeDir + 'assets/';
elixir.config.publicPath = themeDir + 'assets/compiled/';

elixir(function(mix){
	mix.sass('style.scss');
	mix.scripts([
		'uikit.min.js',
		'uikit-icons.min.js',
	]);
	mix.livereload([
		themeDir + 'compiled/**/*',
		themeDir + '**/*.htm',
	]);
});

// var gulp = require('gulp');
// var sassdoc = require('sassdoc');
// var converter = require('sass-convert');
// var ext_replace = require('gulp-ext-replace');
 
// gulp.task('sassdoc', function () {
//   return gulp.src('bulma/**/*.+(sass|scss)')
//     .pipe(converter({
//       from: 'sass',
//       to: 'scss',
//     }))
//    	.pipe(ext_replace('.scss'))
//     .pipe(gulp.dest('./themes/octdemo/assets/sass/bulma'));
// });

// gulp.task('default', ['sassdoc']);