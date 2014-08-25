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
use OCP\AppFramework\Http;

class FxJSONResponse extends JSONResponse {

    public function __construct($data=array(), $statusCode=Http::STATUS_OK){
        parent::__construct($data, $statusCode);
        // Set timestamp in response header
        $this->addHeader('Timestamp', (new \DateTime())->getTimestamp());
    }
}
