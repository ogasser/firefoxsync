/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

'use strict';

define([
  'lib/auth-errors',
  'p-promise',
  'fxaClient'
],
function (AuthErrors, p, fxac) {

    var fxaClient = new fxac();
    

    function createAccount(email, password) {
        alert("einszweidrei " + email + " " + password);
      
        return fxaClient.signUp(email, password)
        .then(function (accountData) {
          return onSignUpSuccess(accountData);
        })
        .then(null, function (err) {
          // Account already exists. No attempt is made at signing the
          // user in directly, instead, point the user to the signin page
          // where the entered email/password will be prefilled.
          if (AuthErrors.is(err, 'ACCOUNT_ALREADY_EXISTS')) {
            console.log('FxSync: Account already exists');
            return;
            ///return self._suggestSignIn(err);
          } else if (AuthErrors.is(err, 'USER_CANCELED_LOGIN')) {
            //self.logEvent('login:canceled');
            // if user canceled login, just stop
            console.log('FxSync: User canceled login');;
            return;
          }

          // re-throw error, it will be handled at a lower level.
          throw err;
        });
    }

    function onSignUpSuccess(accountData) {
      if (accountData.verified) {
        console.log('ACCOUNT VERIFIED');
        this.navigate('settings');
      } else {
        console.log('ACCOUNT NOT VERIFIED');
        this.navigate('confirm');
      }
    }

});
