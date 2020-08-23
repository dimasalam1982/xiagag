<?php

namespace app\models;

use app\database\Database;
use app\request\Request;

class User
{

    const USER_COOKIE_NAME = 'user_uid';

    protected $id;
    protected $uid;
    protected $name;

    public function save(): ?self
    {
        if (!$this->id) {
            $id = (Database::getInstance())->insert('INSERT INTO user SET name=:name, uid=:uid', [
                'name' => $this->name,
                'uid' => $this->uid
            ]);

            $this->id = $id;
        }

        return $this;
    }

    /**
     * User is logged in using cookie variable user_uid which is saved automatically
     * if User not found and it is POST and passed field userName that create new User
     */
    public static function authOrCreateNew(): ?self
    {
        $user = null;

        $userIdentity = $_COOKIE[self::USER_COOKIE_NAME] ?? null;

        if (!empty($userIdentity) && $userIdentity!=false) {
            $user = self::findByUid($userIdentity);
        }

        $userName = Request::post('userName');

        if (empty($user) && !empty($userName)) {
            $user = self::createNewUserAndAuth($userName);
        }

        return $user ? $user : new self;
    }

    public static function findByUid($uid): ?self
    {
        $db = Database::getInstance();

        $userInfo = $db->getRow('SELECT * FROM user WHERE uid=:uid', ['uid' => $uid]);

        if (!$userInfo) {
            return null;
        }

        $user = new self;
        $user->id = $userInfo['id'];
        $user->uid = $userInfo['uid'];
        $user->name = $userInfo['name'];

        return $user;
    }

    public static function createNewUserAndAuth(string $name): self
    {
        $user = new self;
        $user->name = $name;
        $user->uid = uniqid(time());
        $user->save();

        setcookie(self::USER_COOKIE_NAME, $user->uid, time()+60*60*24*999, '/');

        return $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function validate()
    {
        parent::validate();
    }
}