<?php


namespace Service\Auth;


use Service\Auth\Model\KeypairModel;
use Service\Auth\Storage\KeyPairStorage;
use Verse\Run\Util\Uuid;
use Verse\Storage\Spec\Compare;

class AuthService
{
    /**
     * @var \Service\Auth\Storage\KeyPairStorage
     */
    private $keyPairStorage;

    /**
     * @return \Service\Auth\Storage\KeyPairStorage
     */
    public function getKeyPairStorage() : \Service\Auth\Storage\KeyPairStorage
    {
        if (!$this->keyPairStorage) {
            $this->keyPairStorage = new KeyPairStorage();
        }
        return $this->keyPairStorage;
    }

    public function addAuthPair($type, $key, $password, $userId)
    {
        $key = $this->_prepareKey($key);
        $id = $this->_makeId($type, $key);

        $salt = Uuid::v4() . '-' . Uuid::v4() . '-' . Uuid::v4(). '-' . Uuid::v4();
        $hash = hash('sha256', $key . $salt. $password);

        $bind = [
            KeypairModel::TYPE    => $type,
            KeypairModel::KEY     => $key,
            KeypairModel::SALT    => $salt,
            KeypairModel::HASH    => $hash,
            KeypairModel::USER_ID => $userId,
        ];
    
        return $this->getKeyPairStorage()->write()->insert($id, $bind, __METHOD__);
    }
    
    public function getPairByTypeAndKey ($type, $key) 
    {
        return $this->getKeyPairStorage()->search()->findOne(
            [
                [KeypairModel::TYPE, Compare::EQ, $type],
                [KeypairModel::KEY, Compare::EQ, $key]
            ],
            __METHOD__
        );
    }
    
    public function removePairById ($id) 
    {
        return $this->getKeyPairStorage()->write()->remove($id, __METHOD__);
    }

    /**
     * @param $type
     * @param $key
     * @param $pass
     * 
     * @return bool|string
     */
    public function getUserIdByPair ($type, $key, $pass) 
    {
        $key = $this->_prepareKey($key);
        $id = $this->_makeId($type, $key);
        
        $pair = $this->getKeyPairStorage()->read()->get($id, __METHOD__);
        if (!$pair) {
            return false;
        }
        
        if ($key !== $pair[KeypairModel::KEY]) {
            return false;
        }

        $hash = hash('sha256', $key . $pair[KeypairModel::SALT]. $pass);
        if ($hash !== $pair[KeypairModel::HASH]) {
            return false;
        }
        
        return $pair[KeypairModel::USER_ID];
    }

    private function _makeId($type, $key)
    {
        return crc32($type . ':' . $key);
    }

    private function _prepareKey($key)
    {
        return mb_strtolower($key);
    }
}