<?php

namespace OCA\FirefoxSync\Db;

use \OCP\AppFramework\Db\Entity;

class Session extends Entity {

    protected $accountId;
    protected $sessionToken;
    protected $authAt;

    public function __construct() {
        // add types in constructor
        $this->addType('account_id', 'integer');
        $this->addType('auth_at', 'integer');
    }
}
