/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

/* jshint ignore:start */
(function(define){
/* jshint ignore:end */
define(function (require, exports, module, undefined) {
  'use strict';

  var SpeedTrap = {
    init: function (options) {
      options = options || {};
      this.navigationTiming = create(NavigationTiming);
      this.navigationTiming.init(options);

      this.baseTime = this.navigationTiming.get().navigationStart || now();

      this.timers = create(Timers);
      this.timers.init({
        baseTime: this.baseTime
      });

      this.events = create(Events);
      this.events.init({
        baseTime: this.baseTime
      });

      this.uuid = guid();

      this.tags = options.tags || [];

      // store a bit with the site being tracked to avoid sending cookies to
      // rum-diary.org. This bit keeps track whether the user has visited
      // this site before. Since localStorage is scoped to a particular
      // domain, it is not shared with other sites.
      try {
        this.returning = !!localStorage.getItem('_st');
        localStorage.setItem('_st', '1');
      } catch(e) {
        // if cookies are disabled, localStorage access will blow up.
      }
    },

    /**
     * Data to send on page load.
     */
    getLoad: function () {
      // puuid is saved for users who visit another page on the same
      // site. The current page will be updated to set its is_exit flag
      // to false as well as update which page the user goes to next.
      var previousPageUUID;
      try {
        previousPageUUID = sessionStorage.getItem('_puuid');
        sessionStorage.removeItem('_puuid');
      } catch(e) {
        // if cookies are disabled, sessionStorage access will blow up.
      }

      return {
        uuid: this.uuid,
        puuid: previousPageUUID,
        navigationTiming: this.navigationTiming.diff(),
        referrer: document.referrer || '',
        tags: this.tags,
        returning: this.returning,
        screen: {
          width: window.screen.width,
          height: window.screen.height
        }
      };
    },

    /**
     * Data to send on page unload
     */
    getUnload: function () {
      // puuid is saved for users who visit another page on the same
      // site. The current page will be updated to set its is_exit flag
      // to false as well as update which page the user goes to next.
      try {
        sessionStorage.setItem('_puuid', this.uuid);
      } catch(e) {
        // if cookies are disabled, sessionStorage access will blow up.
      }

      return {
        uuid: this.uuid,
        duration: now() - this.baseTime,
        timers: this.timers.get(),
        events: this.events.get()
      };
    }
  };

  var NAVIGATION_TIMING_FIELDS = {
    'navigationStart': undefined,
    'unloadEventStart': undefined,
    'unloadEventEnd': undefined,
    'redirectStart': undefined,
    'redirectEnd': undefined,
    'fetchStart': undefined,
    'domainLookupStart': undefined,
    'domainLookupEnd': undefined,
    'connectStart': undefined,
    'connectEnd': undefined,
    'secureConnectionStart': undefined,
    'requestStart': undefined,
    'responseStart': undefined,
    'responseEnd': undefined,
    'domLoading': undefined,
    'domInteractive': undefined,
    'domContentLoadedEventStart': undefined,
    'domContentLoadedEventEnd': undefined,
    'domComplete': undefined,
    'loadEventStart': undefined,
    'loadEventEnd': undefined
  };

  var navigationTiming;
  try {
    navigationTiming = window.performance.timing;
  } catch (e) {
    navigationTiming = create(NAVIGATION_TIMING_FIELDS);
  }

  var NavigationTiming = {
    init: function (options) {
      options = options || {};
      this.navigationTiming = options.navigationTiming || navigationTiming;

      // if navigationStart is not available (no browser support), use now
      // as the basetime.
      this.baseTime = this.navigationTiming.navigationStart || now();
    },

    get: function () {
      return this.navigationTiming;
    },

    diff: function() {
      var diff = {};
      var baseTime = this.baseTime;
      for (var key in NAVIGATION_TIMING_FIELDS) {
        if ( ! this.navigationTiming[key])
          diff[key] = null;
        else
          diff[key] = this.navigationTiming[key] - baseTime;
      }
      return diff;
    }
  };

  var Timers = {
    init: function (options) {
      this.completed = {};
      this.running = {};
      this.baseTime = options.baseTime;
    },

    start: function (name) {
      var start = now();
      if (this.running[name]) throw new Error(name + ' timer already started');

      this.running[name] = start;
    },

    stop: function (name) {
      var stop = now();

      if (! this.running[name]) throw new Error(name + ' timer not started');

      if (! this.completed[name]) this.completed[name] = [];
      var start = this.running[name];

      this.completed[name].push({
        start: start - this.baseTime,
        stop: stop - this.baseTime,
        elapsed: stop - start
      });

      this.running[name] = null;
      delete this.running[name];
    },

    get: function (name) {
      if (! name) return this.completed;
      return this.completed[name];
    },

    clear: function () {
      this.completed = {};
      this.running = {};
    }
  };

  var Events = {
    init: function (options) {
      this.events = [];
      this.baseTime = options.baseTime;
    },

    capture: function (name) {
      this.events.push({
        type: name,
        offset: now() - this.baseTime
      });
    },

    get: function () {
      return this.events;
    },

    clear: function () {
      this.events = [];
    }
  };

  function create(proto) {
    if (Object.create) return Object.create(proto);

    var F = function () {};
    F.prototype = proto;
    return new F();
  }

  function now() {
    return new Date().getTime();
  }

  function guid() {
    // from http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
      /*jshint bitwise: false*/
      var r = Math.random() * 16|0, v = c === 'x' ? r : (r&0x3|0x8);
      return v.toString(16);
    });
  }

  module.exports = create(SpeedTrap);
});
/* jshint ignore:start */
})((function(n,w){return typeof define=='function'&&define.amd
?define:typeof module=='object'?function(c){c(require,exports,module);}
:function(c){var m={exports:{}},r=function(n){return w[n];};w[n]=c(r,m.exports,m)||m.exports;};
})('SpeedTrap',this));
/* jshint ignore:end */
