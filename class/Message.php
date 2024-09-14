<?php declare(strict_types = 1);
require_once(__DIR__. '/../configs/constClass.php');

class Message
{
    private const INPUT_REQUIRED = ConstClass::INPUT_REQUIRED;
    private const INPUT_ALL_REQUIRED = ConstClass::INPUT_ALL_REQUIRED;
    private const NOT_USE_ALL_ENCODING = ConstClass::NOT_USE_ALL_ENCODING;
    private const NOT_USE_ENCODING = ConstClass::NOT_USE_ENCODING;
    private const NOT_MODEL_NAME_ALL = ConstClass::NOT_MODEL_NAME_ALL;
    private const NOT_MODEL_NAME = ConstClass::NOT_MODEL_NAME;
    private const CHARACTERS_NOT_USED  = ConstClass::CHARACTERS_NOT_USED;
    private const INPUT_MISS = ConstClass::INPUT_MISS;
    private const OUT_OF_RANGE = ConstClass::OUT_OF_RANGE;
    private const VIOLATION_CHAR = ConstClass::VIOLATION_CHAR;
    private const EXCESS_OR_DEFICIENCY_CHAR = ConstClass::EXCESS_OR_DEFICIENCY_CHAR;
    private const USE_NOT_JOE_ACCOUNT = ConstClass::USE_NOT_JOE_ACCOUNT;
    private const CAN_NOT_USER_ID = ConstClass::CAN_NOT_USER_ID;
    private const MISTAKE_USER_ID_PASSWORD = ConstClass::MISTAKE_USER_ID_PASSWORD;
    private const NOT_INTEGER_ONLY = ConstClass::NOT_INTEGER_ONLY;
    private const IMPOSSIBLE_EXTENTION = ConstClass::IMPOSSIBLE_EXTENTION;
    private const IMPOSSIBLE_FILE_SIZE = ConstClass::IMPOSSIBLE_FILE_SIZE;
    private const FILE_ERROR = ConstClass::FILE_ERROR;
    private const DUPLICATION_NAME = ConstClass::DUPLICATION_NAME;
    private const NO_ITEM = ConstClass::NO_ITEM;
    private const UNAUTHORIZED_ACCESS = ConstClass::UNAUTHORIZED_ACCESS;
    private const NOT_DELIVERY = ConstClass::NOT_DELIVERY;
    private const FUNNY_DAY = ConstClass::FUNNY_DAY;
    private const COUNT_ERROR = ConstClass::COUNT_ERROR;
    private const CALC_MISS = ConstClass::CALC_MISS;
    private const NOT_DELETE = ConstClass::NOT_DELETE;
    private const SUCCESS_DELETE = ConstClass::SUCCESS_DELETE;
    private const LACK_POINT = ConstClass::LACK_POINT;
    private const CAN_NOT_TEL = ConstClass::CAN_NOT_TEL;
    public $message;

    public function __construct()
    {
        $this->message = [];
    }


    public function pushEmpty($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::INPUT_REQUIRED;
    }

    public function pushAllEmpty($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::INPUT_ALL_REQUIRED;
    }

    public function notUseALLEncoding($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::NOT_USE_ALL_ENCODING;
    }

    public function notUseEncoding($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::NOT_USE_ENCODING;
    }

    public function notModelNameALL($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::NOT_MODEL_NAME_ALL;
    }

    public function notModelName($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::NOT_MODEL_NAME;
    }

    public function notUsedcharacters($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::CHARACTERS_NOT_USED;
    }

    public function pushMiss($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::INPUT_MISS;
    }

    public function pushOutRange($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::OUT_OF_RANGE;
    }

    public function pushViolationChar($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::VIOLATION_CHAR;
    }

    public function pushExcessOrDeficiencyChar($key){
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::EXCESS_OR_DEFICIENCY_CHAR;
    }

    public function notUseJoeAccount($key){
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::USE_NOT_JOE_ACCOUNT;
    }

    public function canNotUserId($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::CAN_NOT_USER_ID;
    }

    public function notIntegerOnly($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::NOT_INTEGER_ONLY;
    }

    public function impossibleExtension($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::IMPOSSIBLE_EXTENTION;
    }

    public function impossibleFileSize($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::IMPOSSIBLE_FILE_SIZE;
    }

    public function fileError($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::FILE_ERROR;
    }
    public function duplicationItemName($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::DUPLICATION_NAME;
    }

    public function mistakeUserIdPassword($key)
    {
        $this->message[$key] = self::MISTAKE_USER_ID_PASSWORD;
    }

    public function noBuy($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::NO_ITEM;
    }

    public function unauthorizedAccess($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::UNAUTHORIZED_ACCESS;
    }

    public function notDelivery($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::NOT_DELIVERY;
    }
    public function funnyDay($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::FUNNY_DAY;
    }
    public function countError($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::COUNT_ERROR;
    }
    public function calcMiss($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::CALC_MISS;
    }
    public function notDelete($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::NOT_DELETE;
    }

    public function successDelete($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::SUCCESS_DELETE;
    }

    public function lackPoint($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::LACK_POINT;
    }

    public function canNotTel($key)
    {
        if (array_key_exists($key, $this->message)){
            return;
        }
        $this->message[$key] = self::CAN_NOT_TEL;
    }

    public function getMessage()
    {
        return $this->message;
    }
}