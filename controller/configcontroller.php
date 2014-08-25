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
use \OCP\AppFramework\Http\DownloadResponse;
use \OCP\AppFramework\Controller;
use \OCP\Util;

class ConfigController extends Controller {

    public function __construct($appName, IRequest $request){
        parent::__construct($appName, $request);
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        $resp = '{"cookiesEnabled":false,"fxaccountUrl":"https://localhost/owncloud/index.php/apps/firefoxsync/accounts/v1","oauthUrl":"https://oauth.accounts.firefox.com","profileUrl":"https://profile.accounts.firefox.com","oauthClientId":"98e6508e88680e1a","language":"en-us","metricsSampleRate":0.1}';
        return new DownloadResponse("lib/config", "application/json");

    }
}
