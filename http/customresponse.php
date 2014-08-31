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

namespace OCA\FirefoxSync\Http;

use \OCP\AppFramework\Http\Response;

/**
 * Outputs custom data
 */
class CustomResponse extends Response {

	private $content;

	/**
	 * Creates a response that outputs custom data
	 * @param string $content the content that should be returned to the client
	 * @param string $contentType the mimetype.
	 */
	public function __construct($content, $contentType){
		$this->content = $content;
		$this->addHeader('Content-type', $contentType);
	}


	/**
	 * Simply sets the headers and returns the custom data
	 * @return string the custom data
	 */
	public function render(){
		return $this->content;
	}


}
