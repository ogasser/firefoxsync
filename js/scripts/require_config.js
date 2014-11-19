/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require.config({
  baseUrl: '/owncloud/apps/firefoxsync/js/scripts',
  paths: {
    jquery: '../bower_components/jquery/dist/jquery',
    backbone: '../bower_components/backbone/backbone',
    underscore: '../bower_components/underscore/underscore',
    fxaClient: '../bower_components/fxa-js-client/fxa-client',
    text: '../bower_components/requirejs-text/text',
    mustache: '../bower_components/mustache/mustache',
    stache: '../bower_components/requirejs-mustache/stache',
    chai: '../bower_components/chai/chai',
    'p-promise': '../bower_components/p/p',
    sinon: '../bower_components/sinon/index',
    speedTrap: '../bower_components/speed-trap/dist/speed-trap',
    md5: '../bower_components/JavaScript-MD5/js/md5'
  },
  shim: {
    underscore: {
      exports: '_'
    },
    backbone: {
      deps: [
        'underscore',
        'jquery'
      ],
      exports: 'Backbone'
    },
    sinon: {
      exports: 'sinon'
    }
  },
  stache: {
    extension: '.mustache'
  }
});


require([
  './lib/app-start',
  './lib/sign-up'
],
function (AppStart, SignUp) {
  var appStart = new AppStart();
  appStart.startApp();
});
