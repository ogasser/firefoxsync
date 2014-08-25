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

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
$application = new Application();

$application->registerRoutes($this, array('routes' => array(
	array('name' => 'setup#index', 'url' => '/', 'verb' => 'GET'),

    // require.js: Route JS to correct path
	array('name' => 'helper#redirect_js', 'url' => '/js/{path}', 'verb' => 'GET',
    'requirements' => array('path' => '.+')),

    // Content server config
    array('name' => 'config#index', 'url' => '/config', 'verb' => 'GET'),

    // Firefox Accounts Server API
    array('name' => 'accounts#create', 'url' => '/accounts/v1/account/create', 'verb' => 'POST'),
    array('name' => 'accounts#status', 'url' => '/accounts/v1/account/status', 'verb' => 'GET'),
    array('name' => 'accounts#login', 'url' => '/accounts/v1/account/login', 'verb' => 'POST'),
    array('name' => 'accounts#keys', 'url' => '/accounts/v1/account/keys', 'verb' => 'GET'),
)));

