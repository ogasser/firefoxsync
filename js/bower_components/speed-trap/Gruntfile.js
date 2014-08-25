/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

'use strict';

module.exports = function (grunt) {
  // show elapsed time at the end
  require('time-grunt')(grunt);
  // load all grunt tasks
  require('load-grunt-tasks')(grunt);

  grunt.initConfig({});
  grunt.loadTasks('grunttasks');

  grunt.registerTask('build', [
    'jshint',
    'mocha',
    'clean',
    'copy',
    'uglify'
  ]);

  grunt.registerTask('default', [
    'jshint',
    'mocha'
  ]);

  grunt.registerTask('test', [
    'mocha'
  ]);

};


