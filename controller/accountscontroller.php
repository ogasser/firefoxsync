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

namespace OCA\FirefoxSync\Controller;

use \OCP\IRequest;
use \OCP\AppFramework\Controller;
use \OCP\Util;

use \OCA\FirefoxSync\Vendor\Scrypt;
use \OCA\FirefoxSync\Vendor\Hawk;
use \OCA\FirefoxSync\Db\Account;
use \OCA\FirefoxSync\Db\Session;
use \OCA\FirefoxSync\Lib\FxJSONResponse;
use \OCA\FirefoxSync\Lib\FxErrorResponse;

class AccountsController extends Controller {

    const MOZILLA_BASE_HKDF = 'identity.mozilla.com/picl/v1/';

    private $accountMapper;
    private $sessionMapper;
    private $session;

    public function __construct($appName, IRequest $request, $accountMapper, $sessionMapper, $session){
        parent::__construct($appName, $request);
        $this->accountMapper = $accountMapper;
        $this->sessionMapper = $sessionMapper;
        $this->session = $session;
    }


    /**
     *
     * @PublicPage
     * @NoCSRFRequired
     */
    public function create() {

        $email = $this->params('email');
        $authPw = $this->params('authPW');

        // Check if params are missing
        if ($email === NULL || $authPw == NULL) {
            return new FxErrorresponse(400, 108);
        }

        // Check if account with this email address already exists
        if ($this->accountMapper->existsByEmail($email)) {
            return new FxErrorresponse(400, 101);
        }

        // Scrypt
        $authSalt = Scrypt::generateSalt(32);

        $bigStrechedPw = Scrypt::hash($authPw, $authSalt, 64*1024, 8, 1);

        $verifyHash = $this->hkdf($bigStrechedPw, 'sha256', '', 32, $MOZILLA_BASE_HKDF . 'verifyHash');

        // Insert account into DB
        $acc = new Account();
        $acc->setUid(bin2hex(Scrypt::generateSalt(16)));
        $acc->setEmail($email);
        $acc->setKa(bin2hex(Scrypt::generateSalt(32)));
        $acc->setWrapWrapKb(bin2hex(Scrypt::generateSalt(32)));
        $acc->setVerifyHash(bin2hex($verifyHash));
        $acc->setAuthSalt($authSalt);

        $resp = $this->createSession($email, $bigStretchedPw, $acc);

        // Only insert if all previous actions succeeded
        $this->accountMapper->insert($acc);
        return $resp;
    }



    /**
        * * HKDF
        * *
        * * @link https://tools.ietf.org/rfc/rfc5869.txt
        * * @param $key Input key
        * * @param $digest A SHA-2 hashing algorithm
        * * @param $salt Optional salt
        * * @param $length Output length (defaults to the selected digest size)
        * * @param $info Optional context/application-specific info
        * * @return string A pseudo-random key
        * */
    public function hkdf($key, $digest = 'sha512', $salt = NULL, $length = NULL, $info = '')
    {
        if ( ! in_array($digest, array('sha224', 'sha256', 'sha384', 'sha512'), TRUE))
        {
            return FALSE;
        }
         
        $digest_length = substr($digest, 3) / 8;
        if (empty($length) OR ! is_int($length))
        {
            $length = $digest_length;
        }
        elseif ($length > (255 * $digest_length))
        {
            return FALSE;
        }
         
        isset($salt) OR $salt = str_repeat("\0", substr($digest, 3) / 8);
         
        $prk = hash_hmac($digest, $key, $salt, TRUE);
        $key = '';
        for ($key_block = '', $block_index = 1; strlen($key) < $length; $block_index++)
        {
            $key_block = hash_hmac($digest, $key_block.$info.chr($block_index), $prk, TRUE);
            $key .= $key_block;
        }
         
        return substr($key, 0, $length);
    }

    /**
     * Gets the status of an account
     *
     * @PublicPage
     * @NoCSRFRequired
     */
    public function status() {

        $uid = $this->params('uid');

        // uid parameter is required
        if ($uid === NULL) {
            return new FxErrorResponse(400, 108);
        }

        // Remove ownCloud's _route HTTP param
        $params = $this->getParams();
        unset($params['_route']);

        // Additional invalid parameters are provided
        if (count($params) != 1) {
            return new FxErrorresponse(400, 107);
        }

        // Check if account with uid exists
        $exists = true;
        try {
            $this->accountMapper->findByUid($uid);
        } catch (\OCP\AppFramework\Db\DoesNotExistException $dnee) {
            $exists = false;
        }

        return new FxJSONResponse(array('exists' => $exists));
    }

    /**
     *
     * @PublicPage
     * @NoCSRFRequired
     */
    public function login() {
        $email = $this->params('email');
        $authPw = $this->params('authPW');

        // Check if params are missing
        if ($email === NULL || $authPw == NULL) {
            return new FxErrorresponse(400, 108);
        }

        // Check if account with this email address exists
        try {
            $acc = $this->accountMapper->findByEmail($email);
        } catch (\OCP\AppFramework\Db\DoesNotExistException $dnee) {
            return new FxErrorresponse(400, 102);
        }

        // Scrypt
        $bigStrechedPw = Scrypt::hash($authPw, $acc->getAuthSalt(), 64*1024, 8, 1);

        $verifyHash = $this->hkdf($bigStrechedPw, 'sha256', '', 32, $MOZILLA_BASE_HKDF . 'verifyHash');

        // Check whether provided password was correct
        if (bin2hex($verifyHash) != $acc->getVerifyHash()) {
            return new FxErrorresponse(400, 103);
        }

        return $this->createSession($email, $bigStrechtedPw);
    }

    /**
     * Creates a session (used for account creation and login)
     */
    private function createSession($email, $bigStretchedPw, $acc = NULL) {
        if (is_null($acc)) {
            $acc = $this->accountMapper->findByEmail($email);
        }

        $session = new Session();
        $session->setAccountId($acc->getId());
        $session->setSessionToken(bin2hex(Scrypt::generateSalt(32)));
        $session->setAuthAt((new \DateTime())->getTimestamp());


        $resp = array('uid' => $acc->getUid(), 'sessionToken' => $session->getSessionToken(), 'verified' => true, 'authAt' => $session->getAuthAt());

        // Send back keyFetchToken if requested
        if ($this->params('keys') === 'true') {
            $resp['keyFetchToken'] = $this->generateKeyFetchToken($bigStretchedPw, $acc);
        }

        // Only insert if all previous actions succeeded
        $this->sessionMapper->insert($session);

        return new FxJSONResponse($resp);
    }

    /**
     * Generate a keyFetchToken and return it in hex.
     */
    private function generateKeyFetchToken($bigStretchedPw, $acc) {
        $wrapWrapKey = $this->hkdf($bigStrechedPw, 'sha256', '', 32, $MOZILLA_BASE_HKDF .'wrapwrapKey');
        $wrapKb = $wrapWrapKey ^ hex2bin($acc->getWrapWrapKb());

        $keyFetchToken = Scrypt::generateSalt(32);

        // Compute token ID to use it as a key in the shared storage
        $tokenId = substr(bin2hex($this->hkdf($keyFetchToken, 'sha256', '', 3*32, $MOZILLA_BASE_HKDF . 'keyFetchToken')), 0, 64);

        // Store keyFetchToken and wrap(Kb) temporarily in the PHP session
        $this->session->set($tokenId, array($keyFetchToken, $acc->getKa(), $wrapKb));

        return bin2hex($keyFetchToken);
    }


    /**
     *
     * @PublicPage
     * @NoCSRFRequired
     */
    public function keys() {
        $stored = $this->verifyHawk();

        // HAWK verification was unsuccessful
        if ($stored === false) {
            return new FxErrorResponse(400, 109);
        }

        $keyFetchToken = $stored[0];
        $kA = $stored[1];
        $wrapKb = $stored[2];

        $keyRequestKey = substr(bin2hex($this->hkdf($keyFetchToken, 'sha256', '', 3*32, $MOZILLA_BASE_HKDF . 'keyFetchToken')), 128, 64);

        $temp = bin2hex($this->hkdf($keyRequestKey, 'sha256', '', 3*32, $MOZILLA_BASE_HKDF . 'account/keys'));
        $respHmacKey = substr($temp, 0, 64);
        $respXorKey = substr($temp, 64, 128);

        $ciphertext = ($kA . $wrapKb) ^ hex2bin($respXorKey);
        $mac = hash_hmac('sha-256', hex2bin($respHmacKey), $ciphertext, TRUE);

        return new FxJSONResponse(array('bundle' => bin2hex($ciphertext . $mac)));
    }



    private function verifyHawk() {
        $hawk = $this->request->getHeader('Hawk');
        $hawkParts = Hawk::parseHeader($hawk);

        // Get temporarily stored keyFetchToken and wrap(kB)
        $stored = $this->session->get($hawkParts['id']);
        $keyFetchToken = $stored[0];

        $reqHmacKey = substr(bin2hex($this->hkdf($keyFetchToken, 'sha256', '', 3*32, $MOZILLA_BASE_HKDF . 'keyFetchToken')), 64, 64);

        $colonPos = strpos(Util::getServerHost(), ':');
        $port = ($colonPos !== false) ? int(substr(Util::getServerHost(), $colonPos)) : 80;

        // Now validate the request
        $valid = Hawk::verifyHeader($hawk, array(
            'host'   =>  Util::getServerHostName(),
            'port'   =>  $port,
            'path'   =>  Util::getRequestUri(),
            'method' =>  $this->request->getMethod()
        ), $reqHmacKey); // return true if the request is valid, otherwise false

        if (!$valid) {
            return false;
        }

        return $stored;
    }

}
