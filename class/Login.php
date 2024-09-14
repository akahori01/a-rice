<?php declare(strict_types = 1);
require_once(__DIR__. '/Make.php');
require_once(__DIR__. '/Check.php');
require_once(__DIR__. '/Message.php');
require_once(__DIR__. '/Encrypt.php');
require_once(__DIR__. '/../DB/UserModel.php');
require_once(__DIR__. '/../configs/constApp.php');

class Login
{
    private const SIGNUP_USER_ID = ConstApp::SIGNUP_USER_ID;
    private const SIGNUP_PASSWORD = ConstApp::SIGNUP_PASSWORD;

    public $datas;
    public $make;
    public $check;
    public $message;
    public $encrypt;
    public $notUserModel;
    public $isUserModel;
    public $result;
    public $userId;

    public function __construct($datas)
    {
        $this->datas = $datas;
        $this->make = new Make();
        $this->check = new Check();
        $this->message = new Message();
        $this->encrypt = new Encrypt();
        $this->notUserModel = new SelectUserModel(null);
        $this->result = [];
    }

    public function deleteEmpty()
    {
        foreach ($this->datas as $key => $value)
        {
            $this->datas[$key] = $this->make->emptyDel($value);
        }
    }

    public function checkInput()
    {
        if ($this->check->telCheck($this->datas[self::SIGNUP_USER_ID])){
            foreach ($this->datas as $key => $value){
                $this->result[] = $this->check->blankCheck($value);
                $this->result[] = $this->check->useEnglishAndIntegerHalfSize($value);
                $this->result[] = $this->check->valueLength($value);
            }
            $this->selectTelPassword();
        } else{
            foreach ($this->datas as $key => $value){
                $this->result[] = $this->check->blankCheck($value);
                $this->result[] = $this->check->useEnglishAndIntegerHalfSize($value);
                $this->result[] = $this->check->valueLength($value);
            }
            $this->selectUserIdPassword();
        }
    }

    private function selectTelPassword()
    {
        $telandIv = $this->notUserModel->selectTelandIvAll()->getAllTelandIv();
        $tel = $this->encrypt->matchEncrypt($this->datas[self::SIGNUP_USER_ID], $telandIv);
        if (isset($tel)){
            $this->matchPassword($tel, true);
        } else{
            $this->dammyMatchPassword();
        }
    }

    private function selectUserIdPassword()
    {
        $userIdandIv = $this->notUserModel->selectUserIdIvAll()->getAllUserIdIv();
        $this->userId = $this->encrypt->matchEncrypt($this->datas[self::SIGNUP_USER_ID], $userIdandIv);
        if (isset($this->userId)){
            $this->matchPassword($this->userId, false);
        } else {
            $this->dammyMatchPassword();
        }
    }

    private function matchPassword($uniqueValue, $bool)
    {
        $this->isUserModel = new SelectUserModel($uniqueValue);
        $password = $this->isUserModel->selectPassword($bool)->getPassword();
        $this->result[] = password_verify($this->datas[self::SIGNUP_PASSWORD], $password[self::SIGNUP_PASSWORD]);
    }

    private function dammyMatchPassword()
    {
        $dammy = $this->make->makeMyHash($this->datas[self::SIGNUP_PASSWORD]);
        $dammy = null;
        $this->result[] = false;
    }

    public function messageInput()
    {
        if (in_array(false, $this->result, true)){
            $this->message->mistakeUserIdPassword(self::SIGNUP_PASSWORD);
        }
    }

    public function getDatas()
    {
        return $this->datas;
    }

    public function getMessage()
    {
        return $this->message->getMessage();
    }

    public function getUserId()
    {
        if (isset($this->userId)){
            return $this->userId;
        } else{
            $this->userId = $this->notUserModel->fromTelToUserid()->getToUserId();
            return $this->userId;
        }
    }
}