<?php

namespace OCA\FirefoxSync\Db;

use \OCP\IDb;
use \OCP\AppFramework\Db\Mapper;

class AccountMapper extends Mapper {

    public function __construct(IDb $db) {
        parent::__construct($db, 'firefoxsync_accounts');
    }

    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function findByUid($uid) {
        $sql = 'SELECT * FROM `*PREFIX*firefoxsync_accounts` ' .
            'WHERE `uid` = ?';
        return $this->findEntity($sql, array($uid));
    }

    public function findByEmail($email) {
        $sql = 'SELECT * FROM `*PREFIX*firefoxsync_accounts` ' .
            'WHERE `email` = ?';
        return $this->findEntity($sql, array($email));
    }

    public function findAll($limit=null, $offset=null) {
        $sql = 'SELECT * FROM `*PREFIX*firefoxsync_accounts`';
        return $this->findEntities($sql, $limit, $offset);
    }

    public function existsByEmail($email) {
        try {
            $this->findByEmail($email);
            return true;
        } catch (\OCP\AppFramework\Db\DoesNotExistException $dnee) {
            return false;
        }
    }
}

