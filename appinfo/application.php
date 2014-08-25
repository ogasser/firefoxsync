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

namespace OCA\FirefoxSync\AppInfo;


use \OCP\AppFramework\App;

use \OCA\FirefoxSync\Controller\SetupController;
use \OCA\FirefoxSync\Controller\HelperController;
use \OCA\FirefoxSync\Controller\AccountsController;
use \OCA\FirefoxSync\Controller\ConfigController;

use \OCA\FirefoxSync\Db\AccountMapper;
use \OCA\FirefoxSync\Db\SessionMapper;


class Application extends App {


	public function __construct (array $urlParams=array()) {
		parent::__construct('firefoxsync', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('SetupController', function($c) {
			return new SetupController(
				$c->query('AppName'), 
				$c->query('Request'),
				$c->query('UserId')
			);
		});

        $container->registerService('HelperController', function($c) {
            return new HelperController(
                $c->query('AppName'),
                $c->query('Request')
            );
        });

       $container->registerService('AccountsController', function($c) {
            return new AccountsController(
                $c->query('AppName'),
                $c->query('Request'),
				$c->query('AccountMapper'),
				$c->query('SessionMapper'),
				$c->query('Session')
            );
        });
        
        $container->registerService('ConfigController', function($c) {
            return new ConfigController(
                $c->query('AppName'),
                $c->query('Request')
            );
        });

		/**
		 * Core
		 */
		$container->registerService('UserId', function($c) {
			return \OCP\User::getUser();
		});
		
        /**
         * Database Layer
         */
        $container->registerService('AccountMapper', function($c) {
            return new AccountMapper($c->query('ServerContainer')->getDb());
        });
        
        $container->registerService('SessionMapper', function($c) {
            return new SessionMapper($c->query('ServerContainer')->getDb());
        });

        /**
         * Session used for shared key-value store
         */
        $container->registerService('Session', function($c) {
            return $c->query('ServerContainer')->getSession();
        });
	}

}
