<?php

namespace OCA\FirefoxSync\Db;

use \OCP\IDb;
use \OCP\AppFramework\Db\Mapper;

class SessionMapper extends Mapper {

    public function __construct(IDb $db) {
        parent::__construct($db, 'firefoxsync_sessions');
    }

    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function findAllByAccountId($accountId) {
        $sql = 'SELECT * FROM `*PREFIX*firefoxsync_sessions` ' .
            'WHERE `account_id` = ?';
        return $this->findEntities($sql, array($accountId));
    }
}

