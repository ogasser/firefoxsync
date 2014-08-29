/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

'use strict';

define([
  'underscore',
  'views/form',
  'stache!templates/settings/avatar_camera',
  'lib/session',
  'lib/auth-errors',
  'lib/url'
],
function (_, FormView, Template, Session, AuthErrors, Url) {
  // a blank 1x1 png
  var pngSrc = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAAAAAA6fptVAAAACklEQVQYV2P4DwABAQEAWk1v8QAAAABJRU5ErkJggg==';

  var EXPORT_LENGTH = 600;
  var DISPLAY_LENGTH = 240;

  var View = FormView.extend({
    // user must be authenticated to see Settings
    mustAuth: true,

    template: Template,
    className: 'avatar_camera',

    context: function () {
      return {
        avatar: Session.avatar,
        streaming: this.streaming
      };
    },

    initialize: function (options) {
      this.exportLength = options.exportLength || EXPORT_LENGTH;
      this.displayLength = options.displayLength || DISPLAY_LENGTH;
      this.streaming = false;

      var self = this;
      if (this.automatedBrowser) {
        // mock some things out for automated browser testing
        this.streaming = true;
        this._getMedia = function () {
          self.enableSubmitIfValid();
        };
        this.stream = {
          stop: function () {}
        };
        this.takePicture = function () {
          return pngSrc;
        };
      }
    },

    _getMedia: function () {
      var self = this;
      var nav = this.navigator;

      var getUserMedia = nav.getUserMedia ||
                             nav.webkitGetUserMedia ||
                             nav.mozGetUserMedia ||
                             nav.msGetUserMedia;

      if (! getUserMedia) {
        return this.displayError(AuthErrors.toCode('NO_CAMERA'));
      }

      var getMedia = _.bind(getUserMedia, nav);

      getMedia(
        {
          video: true,
          audio: false
        },
        function(stream) {
          self.stream = stream;
          if (nav.mozGetUserMedia) {
            self.video[0].mozSrcObject = stream;
          } else {
            var vendorURL = self.window.URL || self.window.webkitURL;
            self.video[0].src = vendorURL.createObjectURL(stream);
          }
          self.video[0].play();
        },
        function(err) {
          self.displayError(AuthErrors.toCode('NO_CAMERA'));
        }
      );

    },

    afterRender: function () {
      var self = this;

      this._getMedia();

      this.video = this.$('#video');
      this.canvas = this.$('#canvas')[0];
      this.img = this.$('img');
      this.width = 320;
      this.height = 0;

      self.video[0].addEventListener('canplay', function(ev){
        if (!self.streaming) {
          var pos = self.centeredPos(self.width, self.height, self.displayLength);
          self.height = self.video[0].videoHeight / (self.video[0].videoWidth / self.width);
          self.video.width(self.width);
          self.video.height(self.height);
          self.video.css(pos);
          self.canvas.width = self.width;
          self.canvas.height = self.height;
          self.video.removeClass('hidden');
          self.img.addClass('hidden');
          self.streaming = true;

          self.enableSubmitIfValid();
        }
      }, false);
    },

    isValidEnd: function () {
      return this.streaming;
    },

    submit: function () {
      var data = this.takePicture();
      Session.set('avatar', data);

      this.stream.stop();
      delete this.stream;

      this.navigate('settings/avatar');
    },

    beforeDestroy: function () {
      if (this.stream) {
        this.stream.stop();
        delete this.stream;
      }
    },

    takePicture: function takepicture() {
      var w = this.video[0].videoWidth;
      var h = this.video[0].videoHeight;
      var minValue = Math.min(h, w);

      this.canvas.width = this.exportLength;
      this.canvas.height = this.exportLength;

      var pos = this.centeredPos(w, h, minValue);

      this.canvas.getContext('2d').drawImage(
        this.video[0],
        Math.abs(pos.left),
        Math.abs(pos.top),
        minValue,
        minValue,
        0, 0, this.exportLength, this.exportLength
      );

      return this.canvas.toDataURL('image/jpeg', 0.9);
    },

    // Calculates the position offset needed to center a rectangular image
    // in a square container
    centeredPos: function (w, h, max) {
      if (w > h) {
        return { top: 0, left: (max - w) / 2 };
      } else {
        return { top: (max - h) / 2, left: 0 };
      }
    }
  });

  return View;
});
