/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


const minify = require('@node-minify/core');
const fs = require('fs');
const path = require('path');

/* JS Minify */
const terser = require('@node-minify/terser');
const htmlMinifier = require('@node-minify/html-minifier');
const babelMinify = require('@node-minify/babel-minify');
const gcc = require('@node-minify/google-closure-compiler');
const uglifyjs = require('@node-minify/uglify-js');
const noCompress = require('@node-minify/no-compress');

/* CSS & JS minify */
const yui = require('@node-minify/yui');

/* CSS Minify */
const sqwish = require('@node-minify/sqwish');
const crass = require('@node-minify/crass');
const cssnano = require('@node-minify/cssnano');
const dirname = __dirname;

var asset_url = dirname + '/assets/dist';
var filename = 'backend.1.1.7.min';

console.log('css & js minifying...');

var js_files_primary = [
    dirname + '/assets/js/popper.min.js',
    dirname + '/assets/js/bootstrap.js',
    dirname + '/assets/js/chosen.jquery.js'
];
// Using uglifyjs
minify({
    compressor: uglifyjs,
    input: js_files_primary,
    output: asset_url + `/js/${filename}.js`,
    sync: true,
    callback: function (err, min) {
        console.log('Uglifyjs concat multi files');
        console.log('Uglifyjs concat multi files minify file - ' + asset_url + `/js/${filename}.js`);
        console.log('JS minify error - ' + err);
    }
});

var css_files_first = [
    dirname + '/assets/css/bootstrap.css',
    dirname + '/assets/css/admin-style.css',
    dirname + '/assets/css/chosen.css'
];

// Using Sqwish
minify({
    compressor: sqwish,
    input: css_files_first,
    output: asset_url + `/css/${filename}.css`,
    sync: true,
    callback: function (err, min) {
        console.log('Sqwish css concat minify file - ' + asset_url + `/css/${filename}.css`);
        console.log('CSS minify error - ' + err);
    }
});


