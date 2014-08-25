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
use \OCP\AppFramework\Http\RedirectResponse;
use \OCP\AppFramework\Controller;
use \OCP\Util;

class HelperController extends Controller {

    public function __construct($appName, IRequest $request){
        parent::__construct($appName, $request);
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * CSRF check needs to be disabled as requireJS does not pass the request token.
     * This should not be a problem as only JS files are delivered.
     */
    public function redirectJs($path) {
	// Remove index.php/ from path and redirect
	$url = str_replace('index.php/', '', Util::getRequestUri());
	return new RedirectResponse($url);
    }
}
