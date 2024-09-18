<?php
declare(strict_types = 1);

use function Complex\abs;

require_once(__DIR__. '/SelectPdo.php');
require_once(__DIR__. '/UpdatePdo.php');
require_once(__DIR__. '/../class/Encrypt.php');
require_once(__DIR__. '/../configs/constDB.php');
require_once(__DIR__. '/../class/Trait-Show.php');

class SelectUserModel
{
    use Show;
    private const DB_COLUMN_ID = ConstDB::DB_COLUMN_ID;
    private const DB_COLUMN_USER_ID = ConstDB::DB_COLUMN_USER_ID;
    private const DB_COLUMN_USER_ID_IV = ConstDB::DB_COLUMN_USER_ID_IV;
    private const DB_COLUMN_NAME = ConstDB::DB_COLUMN_NAME;
    private const DB_COLUMN_NAME_IV = ConstDB::DB_COLUMN_NAME_IV;
    private const DB_COLUMN_CITY = ConstDB::DB_COLUMN_CITY;
    private const DB_COLUMN_CITY_IV = ConstDB::DB_COLUMN_CITY_IV;
    private const DB_COLUMN_ADDRESS = ConstDB::DB_COLUMN_ADDRESS;
    private const DB_COLUMN_ADDRESS_IV = ConstDB::DB_COLUMN_ADDRESS_IV;
    private const DB_COLUMN_POSTAL_CODE = ConstDB::DB_COLUMN_POSTAL_CODE;
    private const DB_COLUMN_POSTAL_CODE_IV = ConstDB::DB_COLUMN_POSTAL_CODE_IV;
    private const DB_COLUMN_TEL = ConstDB::DB_COLUMN_TEL;
    private const DB_COLUMN_TEL_IV = ConstDB::DB_COLUMN_TEL_IV;
    private const DB_COLUMN_PASSWORD = ConstDB::DB_COLUMN_PASSWORD;
    private const ADMIN_USER_ID = ConstDB::ADMIN_USER_ID;

    private $userId;
    private $toUserId;
    private $select;
    public $update;
    public $encrypt;
    private $selectId;
    private $selectUserId;
    private $selectOnlyUserId;
    private $selectUserIdAll;
    private $selectTelandIvAll;
    private $selectName;
    private $selectCity;
    private $selectAddress;
    private $selectPostalCode;
    private $selectPassword;
    private $tels;
    private $tel;
    private $selectTelAll;
    private $selectUserIdIv;

    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->select = new SelectPdo($this->userId);
        $this->update = new UpdatePdo();
        $this->encrypt = new Encrypt();
    }

    public function checkUserId()
    {
        $this->selectUserIdAll();
        return in_array($this->userId, $this->getUserIdAll(), true);
    }

    public function fromTelToUserid()
    {
        $this->toUserId = $this->select->selectColumn(self::DB_COLUMN_USER_ID, self::DB_COLUMN_TEL);
        return $this;
    }
    public function selectId()
    {
        $this->selectId = $this->select->selectColumn(self::DB_COLUMN_ID, self::DB_COLUMN_USER_ID);
        return $this;
    }

    public function selectUserIdAll()
    {
        $this->selectUserIdAll = $this->select->selectColumnAll(self::DB_COLUMN_USER_ID);
        return $this;
    }

    public function selectTelAll()
    {
        $this->selectTelAll = $this->select->selectColumnAll(self::DB_COLUMN_TEL);
        return $this;
    }

    public function selectUserIdAllComposite()
    {
        $array = $this->select->selectSomeColumnAll(self::DB_COLUMN_USER_ID, self::DB_COLUMN_USER_ID_IV);
        for ($i = 0; $i < count($array); $i++){
            $this->selectUserId[] = $this->encrypt->composite($array[$i]);
        }
        return $this;
    }
    public function selectUserIv()
    {
        $userIvs = $this->select->selectColumnAll(self::DB_COLUMN_USER_ID_IV);
        foreach ($userIvs as $iv){
            $array = [];
            $array = [$this->userId, $iv];
            $userIds[] = $this->encrypt->composite($array);
        }
        return in_array(self::ADMIN_USER_ID, $userIds, true);
    }

    public function selectTelandIvAll(){
        $this->selectTelandIvAll = $this->select->selectSomeColumnAll(self::DB_COLUMN_TEL, self::DB_COLUMN_TEL_IV);
        return $this;
    }

    public function selectUserIdIvAll()
    {
        $this->selectUserIdIv = $this->select->selectSomeColumnAll(self::DB_COLUMN_USER_ID, self::DB_COLUMN_USER_ID_IV);
        return $this;
    }
    public function selectUserId()
    {
        $array = $this->select->selectSomeColumn(self::DB_COLUMN_USER_ID, self::DB_COLUMN_USER_ID_IV);
        $this->selectOnlyUserId = $this->encrypt->composite($array);
        return $this;
    }
    public function selectName()
    {
        $array = $this->select->selectSomeColumn(self::DB_COLUMN_NAME, self::DB_COLUMN_NAME_IV);
        $this->selectName = $this->encrypt->composite($array);
        return $this;
    }
    public function selectCity()
    {
        $array = $this->select->selectSomeColumn(self::DB_COLUMN_CITY, self::DB_COLUMN_CITY_IV);
        $this->selectCity = $this->encrypt->composite($array);
    }
    public function selectAddress()
    {
        $array = $this->select->selectSomeColumn(self::DB_COLUMN_ADDRESS, self::DB_COLUMN_ADDRESS_IV);
        $this->selectAddress = $this->encrypt->composite($array);
        return $this;
    }
    public function selectPostalCode()
    {
        $array = $this->select->selectSomeColumn(self::DB_COLUMN_POSTAL_CODE, self::DB_COLUMN_POSTAL_CODE_IV);
        $this->selectPostalCode = $this->encrypt->composite($array);
        return $this;
    }

    public function selectTelAllComposite()
    {
        $array = $this->select->selectSomeColumnAll(self::DB_COLUMN_TEL, self::DB_COLUMN_TEL_IV);
        for ($i = 0; $i < count($array); $i++){
            $this->tels[] = $this->encrypt->composite($array[$i]);
        }
        return $this;
    }
    public function selectTel()
    {
        $array = $this->select->selectSomeColumn(self::DB_COLUMN_TEL, self::DB_COLUMN_TEL_IV);
        $this->tel = $this->encrypt->composite($array);
        return $this;
    }
    public function selectPassword($bool)
    {
        if ($bool === true){
            $this->selectPassword = $this->select->selectColumn(self::DB_COLUMN_PASSWORD, self::DB_COLUMN_TEL);
        } elseif ($bool === false){
            $this->selectPassword = $this->select->selectColumn(self::DB_COLUMN_PASSWORD, self::DB_COLUMN_USER_ID);
        } else{
            return;
        }
        return $this;
    }


    public function getId()
    {
        return $this->selectId[self::DB_COLUMN_ID];
    }
    public function getAllUserIdComposite()
    {
        return $this->selectUserId;
    }
    public function getUserIdAll()
    {
        return $this->selectUserIdAll;
    }
    public function getAllUserIdIv()
    {
        return $this->selectUserIdIv;
    }
    public function getUserId()
    {
        return $this->selectOnlyUserId;
    }
    public function getTelAll()
    {
        return $this->selectTelAll;
    }
    public function getAllTelandIv()
    {
        return $this->selectTelandIvAll;
    }
    public function getName()
    {
        return $this->selectName;
    }
    public function getCity()
    {
        return $this->selectCity;
    }
    public function getAddress()
    {
        $address = self::separate_address($this->selectAddress);
        return [$address['prefecture'], $address['city'], $address['addr']];
    }
    public function getPostalCode()
    {
        $postalcodeTop = substr($this->selectPostalCode, 0, 3);
        $postalcodeBottom = substr($this->selectPostalCode, 3, 7);
        return [$postalcodeTop, $postalcodeBottom];
    }
    public function getTel()
    {
        return $this->tel;
    }
    public function getAllTelComposite()
    {
        return $this->tels;
    }
    public function getPassword()
    {
        return $this->selectPassword;
    }
    public function getToUserId()
    {
        return $this->toUserId;
    }
    /* user_pointアップデート用
    public function addPointUpdate($updateValue)
    {
        $this->update->updateUserPoint($updateValue, $this->userId);
    }
    */
}