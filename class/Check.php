<?php declare(strict_types = 1);
require_once(__DIR__. '/../configs/constClass.php');

class Check
{

    private const RESULT_TRUE = ConstClass::RESULT_TRUE;
    private const MIN_LENGTH = ConstClass::MIN_LENGTH;
    private const MAX_LENGTH = ConstClass::MAX_LENGTH;

    // 運搬可能な地域
    private const PREFECTURE = ConstClass::PREFECTURE;
    private const CITYS = ConstClass::CITYS;

    private const MIMES = ConstClass::MIMES;
    private const MAX_FILE_SIZE = ConstClass::MAX_FILE_SIZE;


    public function modelNameString($string)
    {
        return is_string($string);
    }
    public function modelNameNumeric($int)
    {
        return is_numeric($int);
    }


    // 空欄確認
    public function blankCheck($value): bool
    {
        if($value !== '' && !is_null($value))
        {
            return true;
        } else
        {
            return false;
        }
    }

    // UTF-8以外のencodingは許可しない
    public function encodingCheck($value): bool
    {
        if (mb_check_encoding($value, 'UTF-8')){
            return true;
        }else {
            return false;
        }
    }


    // 氏名や住所等の文字種制限がない場合
    public function textboxcheck($text): bool
    {
        return (self::RESULT_TRUE === preg_match('/\A[[:^cntrl:]]{1,30}\z/u', $text));
    }



    // 住所内に「静岡県」& 「御前崎市」 or 「菊川市」 or 「掛川市」 が入っているかチェック関数
    // public function addrCheck (string $prefecture, string $city, string $address): bool
    // {
    //     return ($prefecture === self::PREFECTURE) && (in_array($city, self::CITYS, TRUE)) && ($address !== '');
    // }

    public function prefectureCheck(string $prefecture): bool
    {
        return ($prefecture === self::PREFECTURE);
    }

    public function cityCheck(string $city): bool
    {
        return (in_array($city, self::CITYS, true));
    }


    public function postalTopCheck($postalTop): bool
    {
        return (self::RESULT_TRUE === preg_match('/\A[0-9０-９]{3}\z/u', strval($postalTop)));
    }
    public function postalBottomCheck($postalBottom): bool
    {
        return (self::RESULT_TRUE === preg_match('/\A[0-9０-９]{4}\z/u', strval($postalBottom)));
    }

    // 電話番号が数字のみ(半角全角問わないで)且つ、桁数が合っているかチェック関数
    public function telCheck($tel): bool
    {
        return (self::RESULT_TRUE === preg_match('/\A[0-9０-９]{11}\z/u', strval($tel)));
    }

    // 引数が半角の英字と数字の両方を含んでいるかチェック関数
    public function useEnglishAndIntegerHalfSize($value): bool
    {
        $v = strval($value);
        return (self::RESULT_TRUE === preg_match('/\A[a-zA-Z0-9]+\z/u', $v) && self::RESULT_TRUE === preg_match('/[a-zA-Z]+/u', $v) && self::RESULT_TRUE === preg_match('/[0-9]+/u', $v));
    }

    // パスワードが8文字以上20文字以下のチェック関数
    public function valueLength($password): bool
    {
        return self::MIN_LENGTH <= strlen(strval($password)) && strlen(strval($password)) <= self::MAX_LENGTH;
    }


    // user_id, password が同じのジョーアカウントであるか？
    public function differentJoeAccount($id, $pass){
        return $id !== $pass;
    }

    public function onlyInteger($integer)
    {
        $integer = preg_match('/\A[0-9０-９]+\z/u', strval($integer));
        return $integer === self::RESULT_TRUE;
    }

    public function extensionType($fileType, $filePass)
    {
        $fileType = strtolower($fileType);
        $f_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($f_info, $filePass);
        finfo_close($f_info);
        $mime = strtolower($mime);
        if ($fileType === $mime){
            return $mime;
        } else {
            return false;
        }
    }

    public function fileSize($fileSize)
    {
        return ($fileSize < self::MAX_FILE_SIZE);
    }

    public function emptyImage($filePass)
    {
        return (file_exists($filePass));
    }

    public function DuplicationName($newItemName, $alreadyItemNames)
    {
        return !in_array($newItemName, $alreadyItemNames, true);
    }

    public function allInt($int){
        return (self::RESULT_TRUE === preg_match('/\A[0-9０-９]+\z/u', strval($int)));
    }
}