<?php

namespace OCA\FirefoxSync\Db;

use \OCP\AppFramework\Db\Entity;

class Account extends Entity {

    protected $uid;
    protected $email;
    protected $ka;
    protected $wrapWrapKb;
    protected $verifyHash;
    protected $authSalt;

    public function __construct() {
        // add types in constructor
    }
}
