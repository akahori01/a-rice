<?php declare(strict_types = 1);


require_once(__DIR__. '/Check.php');
require_once(__DIR__. '/Make.php');
require_once(__DIR__. '/Message.php');
require_once(__DIR__. '/Trait-Show.php');
require_once(__DIR__. '/../DB/UserModel.php');
require_once(__DIR__. '/../DB/InsertPdo.php');
require_once(__DIR__. '/../DB/UpdatePdo.php');
require_once(__DIR__. '/../configs/constApp.php');
require_once(__DIR__. '/../configs/constClass.php');
require_once(__DIR__. '/../class/Trait-Show.php');

class SignUp
{
    use Show;
    private const SIGNUP_NAME = ConstApp::SIGNUP_NAME;
    private const SIGNUP_PREF = ConstApp::SIGNUP_PREF;
    private const SIGNUP_CITY = ConstApp::SIGNUP_CITY;
    private const SIGNUP_ADDR = ConstApp::SIGNUP_ADDR;
    private const SIGNUP_POSTAL_TOP = ConstApp::SIGNUP_POSTAL_TOP;
    private const SIGNUP_POSTAL_BOTTOM = ConstApp::SIGNUP_POSTAL_BOTTOM;
    private const SIGNUP_TEL = ConstApp::SIGNUP_TEL;
    private const SIGNUP_USER_ID = ConstApp::SIGNUP_USER_ID;
    private const SIGNUP_PASSWORD = ConstApp::SIGNUP_PASSWORD;
    private const SIGNUP_PASSWORD_HASH = ConstApp::SIGNUP_PASSWORD_HASH;
    private const SIGNUP_ADDRESS = ConstApp::SIGNUP_ADDRESS;
    private const SIGNUP_POSTAL_CODE = ConstApp::SIGNUP_POSTAL_CODE;
    private const SIGNUP_CODE = ConstApp::SIGNUP_CODE;
    private const SIGNUP_SECRET = ConstApp::SIGNUP_SECRET;
    private const SIGNUP_ALL_MESSAGE = ConstApp::SIGNUP_ALL_MESSAGE;
    private const SIGNUP_ENCODING = ConstApp::SIGNUP_ENCODING;


    private const EMPTY_ARRAY = ConstClass::EMPTY_ARRAY;
    private const SUCCESS_VALUE =ConstClass::SUCCESS_VALUE;


    private $datas;
    private $check;
    private $make;
    private $message;
    private $userModel;
    private $insert;
    private $update;
    private $selectUserId;
    private $selectTel;
    private $arrayDatasAllElementCouont;

    public function __construct($datas)
    {
        $this->datas = $datas;
        $this->arrayDatasAllElementCouont = count($this->datas);
        $this->check = new Check();
        $this->make = new Make();
        $this->message = new Message();
        $this->userModel = new SelectUserModel(null);
        $this->insert = new InsertPdo();
        $this->update = new UpdatePdo();
    }


    public function signupCheck($tel_on_data = null, $user_id_on_data = null)
    {
        // 空白除去
        $this->deleteEmpty();
        // エンコーディング修正
        $this->encodingChange();


        // 型名確認
        $this->modelNameCheck();
        if(empty($this->datas)){
            return;
        }
        // 空白確認
        $this->blankCheckMessage();
        if(empty($this->datas)){
            return;
        }
        // エンコーディング確認 (UTF-8のみ)
        $this->encodingCheckMessage();
        if(empty($this->datas)){
            return;
        }
        // その他確認項目
        $this->differentOtherMessagea($tel_on_data, $user_id_on_data);
        if(empty($this->datas)){
            return;
        }
        // チェック項目をすべてクリアしているか確認
        if($this->getMessage() !== []){
            return;
        }
        // 入力値の合成
        $this->makeInputValue();
        // 出力用エスケープ処理
        $this->makeEscape();
    }


    private function deleteEmpty()
    {
        foreach ($this->datas as $key => $value){
            $this->datas[$key] = $this->make->emptyDel($value);
        }
    }

    private function encodingChange()
    {
        $this->datas = mb_convert_encoding($this->datas, 'UTF-8');
    }

    private function blankCheckMessage(){
        $blankMessageArray = [];
        foreach ($this->datas as $key => $value){
            if ($this->check->blankCheck($value)){
                continue;
            }else {
                array_push($blankMessageArray, $key);
            }
        }
        if(!empty($blankMessageArray) && $this->arrayDatasAllElementCouont === count($blankMessageArray)){
            $this->message->pushAllEmpty(self::SIGNUP_ALL_MESSAGE);
            unset($this->datas);
            return;
        } elseif(!empty($blankMessageArray)){
            $this->specificMessage($blankMessageArray, 'pushEmpty');
        } else{
            return $blankMessageArray;
        }
    }
    private function encodingCheckMessage(){
        $encoding_check_array = [];
        foreach($this->datas as $key => $value){
            if ($this->check->encodingCheck($value)){
                continue;
            }else {
                array_push($encoding_check_array, $key);
            }
        }
        if (!empty($encoding_check_array) && $this->arrayDatasAllElementCouont === count($encoding_check_array)){
            $this->message->notUseALLEncoding(self::SIGNUP_ALL_MESSAGE);
            unset($this->datas);
            return;
        } elseif(!empty($encoding_check_array)){
            $this->specificMessage($encoding_check_array, 'notUseEncoding');
        } else{
            return $encoding_check_array;
        }
    }

    private function modelNameCheck()
    {
        // model_name_check内には型名がstringでない(許可しない型名)値のkeyが配列内に入る、
        // $this->arrayDatasAllElementCouontには受け取った配列内の値のcount数である
        $model_name_check = [];
        foreach($this->datas as $key => $value){
            if ($this->check->modelNameString($value)){
                continue;
            }else {
                array_push($model_name_check, $key);
            }
        }
        // 許可しない型名のdata数と、data内のcount数が同数である場合、$this->datasを抹消しretrun
        if (!empty($model_name_check) && $this->arrayDatasAllElementCouont === count($model_name_check)){
            $this->message->notModelNameALL(self::SIGNUP_ALL_MESSAGE);
            unset($this->datas);
            return;
        } elseif(!empty($model_name_check)){
            // 許可しない型名のみメッセージを入れる
            $this->specificMessage($model_name_check, 'notModelName');
        } else{
            return $model_name_check;
        }
    }

    private function specificMessage(array $array, $abnormalName)
    {
        foreach($array as $key){
            switch ($key){
                case self::SIGNUP_PREF:
                case self::SIGNUP_CITY:
                case self::SIGNUP_ADDR:
                    $this->message->$abnormalName(self::SIGNUP_ADDRESS);
                    break;
                case self::SIGNUP_POSTAL_TOP:
                case self::SIGNUP_POSTAL_BOTTOM:
                    $this->message->$abnormalName(self::SIGNUP_POSTAL_CODE);
                    break;
                default:
                    $this->message->$abnormalName($key);
            }
        }
    }

    private function differentOtherMessagea($tel_on_data, $user_id_on_data)
    {
        foreach($this->datas as $key => $value){
            switch($key){
                case self::SIGNUP_NAME:
                    if(!$this->check->textboxcheck($value)){
                        $this->message->notUsedcharacters($key);
                        unset($this->datas[$key]);
                    }
                    break;
                case self::SIGNUP_PREF:
                    if(!$this->check->prefectureCheck($value)){
                        $this->message->pushOutRange(self::SIGNUP_ADDRESS);
                        unset($this->datas[$key]);
                    }
                    break;
                case self::SIGNUP_CITY:
                    if(!$this->check->cityCheck($value)){
                        $this->message->pushOutRange(self::SIGNUP_ADDRESS);
                        unset($this->datas[$key]);
                    }
                    break;
                case self::SIGNUP_ADDR:
                    if(!$this->check->textboxcheck($value)){
                        $this->message->pushOutRange(self::SIGNUP_ADDRESS);
                        unset($this->datas[$key]);
                    }
                    break;
                case self::SIGNUP_POSTAL_TOP:
                    if(!$this->check->postalTopCheck($value)){
                        $this->message->pushMiss(self::SIGNUP_POSTAL_CODE);
                        unset($this->datas[$key]);
                    }
                    break;
                case self::SIGNUP_POSTAL_BOTTOM:
                    if(!$this->check->postalBottomCheck($value)){
                        $this->message->pushMiss(self::SIGNUP_POSTAL_CODE);
                        unset($this->datas[$key]);
                    }
                    break;
                case self::SIGNUP_TEL:
                    if(!$this->check->telCheck($value)){
                        $this->message->pushMiss($key);
                        unset($this->datas[$key]);
                    } elseif(!$this->duplicateTel($key)){
                        if($tel_on_data === $value){
                            break;
                        }else{
                            $this->message->canNotTel($key);
                            unset($this->datas[$key]);
                        }
                    }
                    break;
                case self::SIGNUP_USER_ID:
                    if(!$this->check->useEnglishAndIntegerHalfSize($value)){
                        $this->message->pushViolationChar($key);
                        unset($this->datas[$key]);
                    } elseif(!$this->check->valueLength($value)){
                        $this->message->pushExcessOrDeficiencyChar($key);
                        unset($this->datas[$key]);
                    } elseif(!empty($this->datas[self::SIGNUP_PASSWORD])){
                        if (!$this->check->differentJoeAccount($value, $this->datas[self::SIGNUP_PASSWORD])){
                            $this->message->notUseJoeAccount($key);
                            unset($this->datas[$key]);
                            unset($this->datas[self::SIGNUP_PASSWORD]);
                        }
                    } elseif(!$this->duplicateUserId($key)){
                        if($user_id_on_data === $value){
                            break;
                        }else{
                            $this->message->canNotUserId($key);
                            unset($this->datas[$key]);
                        }
                    }
                    break;
                case self::SIGNUP_PASSWORD:
                    if(!$this->check->useEnglishAndIntegerHalfSize($value)){
                        $this->message->pushViolationChar($key);
                        unset($this->datas[$key]);
                    } elseif(!$this->check->valueLength($value)){
                        $this->message->pushExcessOrDeficiencyChar($key);
                        unset($this->datas[$key]);
                    }
                    break;
            }
        }
    }



    private function makeInputValue()
    {
        $this->datas[self::SIGNUP_ADDRESS] = $this->make->mergeAddr($this->datas[self::SIGNUP_PREF], $this->datas[self::SIGNUP_CITY], $this->datas[self::SIGNUP_ADDR]);
        $this->datas[self::SIGNUP_TEL] = $this->make->halfChar($this->datas[self::SIGNUP_TEL]);
        $this->datas[self::SIGNUP_POSTAL_CODE] = $this->make->mergePostal($this->datas[self::SIGNUP_POSTAL_TOP], $this->datas[self::SIGNUP_POSTAL_BOTTOM]);
    }



    private function duplicateUserId($key)
    {
        $this->selectUserId = $this->userModel->selectUserIdAllComposite()->getAllUserIdComposite();
        return ($this->selectUserId === self::EMPTY_ARRAY || !in_array($this->datas[$key], $this->selectUserId, true));
    }
    private function duplicateTel($key)
    {
        $this->selectTel = $this->userModel->selectTelAllComposite()->getAllTelComposite();
        return ($this->selectTel === self::EMPTY_ARRAY || !in_array($this->datas[$key], $this->selectTel, true));
    }


    private function makeEscape()
    {
        foreach ($this->datas as $key => $value)
        {
            $this->datas[$key] = $this->escape($value);
        }
    }

    public function getMessage()
    {
        return $this->message->getMessage();
    }

    public function getDatas()
    {
        return $this->datas;
    }

    public function getPostalCode()
    {
        return $this->postalCodeFormat($this->datas[self::SIGNUP_POSTAL_CODE]);
    }

    public function getTel()
    {
        return $this->telFormat($this->datas[self::SIGNUP_TEL]);
    }

    public function writeSignupDate($IPaddress, $url, $password_on_data = null, $id_on_data = null, $tel_on_data = null, $user_id_on_data = null)
    {
        $this->signupCheck($tel_on_data, $user_id_on_data);
        if($this->getMessage() !== []){
            return;
        }
        $this->hideData($password_on_data);
        $databaseUserIdAll = $this->userModel->selectUserIdAll()->getUserIdAll();
        $databaseTelAll = $this->userModel->selectTelAll()->getTelAll();
        if ($databaseUserIdAll === self::EMPTY_ARRAY && $databaseTelAll === self::EMPTY_ARRAY){
            $this->insertSignUp($IPaddress, $url);
            return;
        } elseif(isset($tel_on_data) && isset($user_id_on_data)){
            $this->update($id_on_data, $IPaddress, $url);
            return;
        } elseif (in_array($this->datas[self::SIGNUP_SECRET][self::SIGNUP_USER_ID], $databaseUserIdAll, true) || in_array($this->datas[self::SIGNUP_SECRET][self::SIGNUP_TEL], $databaseTelAll, true)){
            header('Location: ../public/error.php');
            exit();
        } else{
            $this->insertSignUp($IPaddress, $url);
            return;
        }
    }

    private function hideData($password_on_data)
    {
        if (empty($this->datas[self::SIGNUP_PASSWORD])){
            $this->setSecret(self::SIGNUP_PASSWORD_HASH, $password_on_data);
        } else{
            $secretPassword = $this->make->makeMyHash($this->datas[self::SIGNUP_PASSWORD]);
            $this->setSecret(self::SIGNUP_PASSWORD_HASH, $secretPassword);
        }
        foreach ($this->datas as $key => $value)
        {
            switch ($key)
            {
                case self::SIGNUP_USER_ID;
                case self::SIGNUP_NAME;
                case self::SIGNUP_ADDRESS:
                case self::SIGNUP_POSTAL_CODE:
                case self::SIGNUP_TEL;
                    list($secretValue, $ivValue) = $this->userModel->encrypt->crypt($value);
                    $this->setSecret($key, $secretValue);
                    $this->setSecret($key. 'Iv', $ivValue);
            }
        }

    }


    public function againHideData()
    {
        foreach ($this->datas as $key => $value){
            switch ($key)
            {
                case self::SIGNUP_NAME;
                case self::SIGNUP_ADDRESS:
                case self::SIGNUP_POSTAL_CODE:
                case self::SIGNUP_TEL;
                    list($secretValue, $ivValue) = $this->userModel->encrypt->crypt($value);
                    $this->setSecret($key, $secretValue);
                    $this->setSecret($key. 'Iv', $ivValue);
            }
        }
    }

    public function update($id_on_data, $IPaddress, $url)
    {
        // var_dump($this->datas[ConstApp::SIGNUP_SECRET][ConstApp::SIGNUP_PASSWORD_HASH]);
        $this->update->updateUserInfo($this->datas, $id_on_data, $IPaddress, $url);
    }

    private function setSecret($key, $secretValue)
    {
        $this->datas[self::SIGNUP_SECRET][$key] = $secretValue;
    }

    private function insertSignUp($IPaddress, $url)
    {
        $this->insert->insertSignUp($this->datas, $IPaddress, $url);
    }

    public function setUserId()
    {
        return $this->datas[self::SIGNUP_SECRET][self::SIGNUP_USER_ID];
    }
}