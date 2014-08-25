<?php
/**
 * ownCloud - firefoxsync
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Oliver Gasser <oliver@flowriver.net>
 * @copyright Oliver Gasser 2014
 */

namespace OCA\FirefoxSync\Lib;

use \OCP\AppFramework\Http\JSONResponse;

class FxErrorResponse extends JSONResponse {

    public function __construct($code, $errno) {

        $error = self::$errors[$code];
        $message = self::$messages[$code][$errno];

        $response = array('code' => $code, 'errno' => $errno, 'error' => $error, 'message' => $message);
        parent::__construct($response, $code);
    }

    // https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
    private static $errors = array(
        400 => "Bad Request",
        401 => "Unauthorized",
        410 => "Gone",
        411 => "Length Required",
        413 => "Request Entity Too Large",
        429 => "Too Many Requests",
        503 => "Service Unavailable",
    );

    // https://github.com/mozilla/fxa-auth-server/blob/master/docs/api.md#response-format
    private static $messages = array(
        400 => array(
            101 => "attempt to create an account that already exists",
            102 => "attempt to access an account that does not exist",
            103 => "incorrect password",
            104 => "attempt to operate on an unverified account",
            105 => "invalid verification code",
            106 => "request body was not valid json",
            107 => "request body contains invalid parameters",
            108 => "request body missing required parameters",
            117 => "incorrect login method for this account",
            118 => "incorrect key retrieval method for this account",
            119 => "incorrect API version for this account",
            120 => "incorrect email case",
        ),
        401 => array(
            109 => "invalid request signature",
            110 => "invalid authentication token",
            111 => "invalid authentication timestamp",
            115 => "invalid authentication nonce",
        ),
        410 => array(
            116 => "endpoint is no longer supported",
        ),
        411 => array(
            112 => "content-length header was not provided",
        ),
        413 => array(
            113 => "request body too large",
        ),
        429 => array(
            114 => "client has sent too many requests (see backoff protocol)",
        ),
        503 => array(
            201 => "service temporarily unavailable to due high load (see backoff protocol)",
        ),
    );
}
