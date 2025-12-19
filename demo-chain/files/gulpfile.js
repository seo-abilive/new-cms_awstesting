const gulp = require('gulp');
const plumber = require('gulp-plumber');
const compass = require('gulp-compass');
const cleanCSS = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');
const uglify = require('gulp-uglify');
const notify = require('gulp-notify');
const gulpif = require('gulp-if');
const changed = require('gulp-changed');
const minimist = require('minimist');
const browserify = require('browserify');
const watchify = require('watchify');
const babelify = require('babelify');
const tsify = require('tsify');
const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
const gzip = require('gulp-gzip');
const rename = require('gulp-rename');
const imagemin = require('gulp-imagemin');
const imageminPngquant = require('imagemin-pngquant');
const imageminMozjpeg = require('imagemin-mozjpeg');
const imageminWebp = require('imagemin-webp');
const imageminGifsicle = require('imagemin-gifsicle');
const svgmin = require('gulp-svgmin');
const browserSync = require('browser-sync');
/**
 * 
 * settings
 *  
 **/
let paths = {
	srcDir: 'images',
	dstDir: '../images_dist'
}
let envSettings = {
	string: 'env',
	default: {
		env: process.env.NODE_ENV || 'development'
	}
}
let srcs = {
	src: './babel/',
	dist: './js/',
	files: ['bundle1.js', 'bundle2.ts']
}
let options = minimist(process.argv.slice(2), envSettings);
let production = options.env === 'production';
let envimagemin = options.env === 'imagemin';
let config = { envProduction: production, envImagemin: envimagemin };


/**
 * 
 * scss
 *  
 **/
// compass + css圧縮 task
gulp.task('compass', function (cb) {
	/**
	 * @var		object	*.scss').pipe(plumber(
	 */
	gulp.src('./sass/**/*.scss').pipe(plumber({
		errorHandler: function (error) {
			notify.onError("Error: <%= error.message %>");
			console.log(error.message);
			this.emit('end');
			return;
		}
	})).pipe(compass({
		config_file: './config.rb',
		css: './css/',
		sass: './sass/'
	}))
	// .pipe(autoprefixer({
	// 	overrideBrowserslist: ['> 1%, last 2 versions, Firefox ESR'],
	// 	cascade: false
	// }))
	.pipe(changed('./css/')).pipe(cleanCSS()).pipe(gulp.dest('./css/'));
	cb();
});
// gzip task
gulp.task('gzip-css', function (cb) {
	gulp.src('./css/*.css').pipe(plumber({
		errorHandler: function (error) {
			notify.onError("Error: <%= error.message %>");
			console.log(error.message);
			this.emit('end');
			return;
		}
	})).pipe(gulpif(config.envProduction, gzip())).pipe(gulpif(config.envProduction, gulp.dest('./css/')));
	cb();
});
/**
 * 
 * javascript
 *  
 **/
// 圧縮 uglify task
gulp.task('uglify-js', function (cb) {
	gulp.src(['./js/*.js', '!./js/*.min.js']).pipe(plumber({
		errorHandler: function (error) {
			notify.onError("Error: <%= error.message %>");
			console.log(error.message);
			this.emit('end');
			return;
		}
	})).pipe(uglify())
		.on('error', function (e) {
			console.log(e)
		}).pipe(rename({ extname: '.min.js' })).pipe(gulp.dest('./js/'));
	cb();
});
// gzip task
gulp.task('gzip-js', function (cb) {
	gulp.src('./js/*.min.js').pipe(plumber({
		errorHandler: function (error) {
			notify.onError("Error: <%= error.message %>");
			console.log(error.message);
			this.emit('end');
			return;
		}
	})).pipe(gulpif(config.envProduction, uglify()))
		.on('error', function (e) {
			console.log(e)
		}).pipe(gulpif(config.envProduction, gzip())).pipe(gulpif(config.envProduction, gulp.dest('./js/')));
	cb();
});
//babel task
gulp.task('babel', function (cb) {
	srcs.files.forEach(function (entryPoint) {
		let bundler = watchify(
			browserify({
				cache: {},
				entries: [srcs.src + entryPoint],
				debug: !production ? true : false,
				packageCache: {},
				extensions: ['.ts', '.js']
			})
		);
		function bundled() {
			return bundler
				.plugin(tsify, { noImplicitAny: true })
				.transform(babelify, { presets: ['@babel/preset-env'] })
				.bundle()
				.pipe(source(entryPoint))
				.pipe(buffer())
				.pipe(rename({ extname: '.js' }))
				.pipe(gulp.dest(srcs.dist));
		};
		bundler.on('update', bundled);
		bundler.on('log', function (message) { console.log(message) });
		return bundled();
	});
	cb();
});
/**
 * 
 * browserSync
 *  
 **/
gulp.task('browserSync', function (cb) {
	browserSync({
		port: 3000,
		proxy: 'localhost',
		open: false,
		files: [
			"!../admin/",
			"../**/*.php",
			"../**/*.html",
			"./**/*.css",
			"./js/**/*.js"
		]
	});
	cb();
});
/**
 * 
 * images 圧縮
 *  
 **/
gulp.task('imagemin', function (cb) {
	let srcGlob = '../**/' + paths.srcDir + '/**/*.+(jpg|jpeg|png|gif)';
	let srcGlob_mod = '!../**/node_modules/**/*.+(jpg|jpeg|png|gif)';
	let dstGlob = paths.dstDir;
	gulp.src([srcGlob, srcGlob_mod]).pipe(gulpif(config.envImagemin, imagemin([
		imageminMozjpeg({
			quality: 85
		}),
		imageminPngquant({
			quality: [0.3, 0.5]
		}),
		imageminGifsicle(),
		imageminWebp({
			quality: 85
		})
	]))).pipe(gulpif(config.envImagemin, gulp.dest(dstGlob)));
	cb();
});
gulp.task('svgmin', function (cb) {
	let srcGlob = '../**/' + paths.srcDir + '/**/*.+(svg)';
	let srcGlob_mod = '!../**/node_modules/**/*.+(svg)';
	let dstGlob = paths.dstDir;
	gulp.src([srcGlob, srcGlob_mod]).pipe(gulpif(config.envImagemin, svgmin())).pipe(gulpif(config.envImagemin, gulp.dest(dstGlob)));
	cb();
});

// タスクを監視
gulp.task('watch', function (cb) {
	// scss & css min
	gulp.watch('./sass/*.scss', gulp.series('compass'));
	// gulp.watch('./css/*.css', gulp.series('gzip-css'));
	//js min
	gulp.watch(['./js/*.js', '!./js/*.min.js'], gulp.series('uglify-js'));
	// gulp.watch(['./js/*.min.js'], gulp.series('gzip-js'));
	gulp.watch(['./babel/*.js', './babel/*.ts'], gulp.series('babel'));
	cb();
});

// タスクを実行する
// gulp.task('default', gulp.series('watch', 'browserSync', 'gzip-js', 'gzip-css', 'imagemin', 'svgmin'));
gulp.task('default', gulp.series('watch', 'browserSync'));