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
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Controller;
use \OCP\Util;

class SetupController extends Controller {

    private $userId;

    public function __construct($appName, IRequest $request, $userId){
        parent::__construct($appName, $request);
        $this->userId = $userId;
    }


    /**
     * CAUTION: the @Stuff turn off security checks, for this page no admin is
     *          required and no CSRF check. If you don't know what CSRF is, read
     *          it up in the docs or you might create a security hole. This is
     *          basically the only required method to add this exemption, don't
     *          add it to any other method if you don't exactly know what it does
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {

        $params = array('user' => $this->userId);
        return $this->render('main', $params);  // templates/main.php
    }


    /**
     * @NoAdminRequired
     */
    public function doEcho() {
        // simply method that posts back the payload of the request
        $echo = $this->params('echo');

        return new JSONResponse(array('echo' => $echo));
    }


}
