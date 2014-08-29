/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

'use strict';

define([
  'jquery',
  'underscore',
  'lib/promise',
  'lib/session',
  'lib/config-loader',
  'lib/oauth-client',
  'lib/assertion',
  'lib/auth-errors'
],
function ($, _, p, Session, ConfigLoader, OAuthClient, Assertion, AuthErrors) {

  function ProfileClient(options) {
    this.profileUrl = options.profileUrl;

    // an OAuth access token
    this.token = options.token;
  }

  ProfileClient.prototype._request = function (path, type, data, headers) {
    var url = this.profileUrl;

    var request = {
      url: url + path,
      type: type,
      headers: {
        Authorization: 'Bearer ' + this.token
      }
    };

    if (data) {
      request.data = data;
    }
    if (headers) {
      _.merge(request.headers, headers);
    }

    return p.jQueryXHR($.ajax(request))
      .then(null, function(xhr) {
        var err = ProfileErrors.normalizeXHRError(xhr);
        throw err;
      });
  };

  // Returns the user's profile data
  // including: email, uid
  ProfileClient.prototype.getProfile = function () {
    return this._request('/v1/profile', 'get');
  };

  // Returns remote image data
  ProfileClient.prototype.getRemoteImage = function (url) {
    return this._request('/v1/remote_image/' + encodeURIComponent(url), 'get');
  };

  var t = function (msg) {
    return msg;
  };

  var ERROR_TO_CODE = {
    UNAUTHORIZED: 100,
    INVALID_PARAMETER: 101,
    // local only errors.
    SERVICE_UNAVAILABLE: 998,
    UNEXPECTED_ERROR: 999
  };

  var CODE_TO_MESSAGES = {
    // errors returned by the profile server
    100: t('Unexpected error'),
    101: t('Invalid parameter in request body: %(param)s'),
    // local only errors.
    998: t('System unavailable, try again soon'),
    999: t('Unexpected error')
  };

  var ProfileErrors = ProfileClient.Errors = _.extend({}, AuthErrors, {
    ERROR_TO_CODE: ERROR_TO_CODE,
    CODE_TO_MESSAGES: CODE_TO_MESSAGES
  });

  return ProfileClient;
});

