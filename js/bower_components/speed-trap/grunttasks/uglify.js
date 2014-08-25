/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

const path = require('path');

module.exports = function (grunt) {
  'use strict';

  grunt.config('uglify', {
    options: {
      banner: grunt.file.read(path.join(__dirname, '..', 'LICENSE'))
    },
    dist: {
      files: {
        'dist/speed-trap.min.js': [ 'src/speed-trap.js' ]
      }
    }
  });
};

