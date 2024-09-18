<?php declare(strict_types = 1);
require_once(__DIR__. '/../configs/constClass.php');
require_once(__DIR__. '/ImageExtension.php');

class Make
{
    private const MIN_VALUE = ConstClass::MIN_VALUE;
    private const MAX_VALUE = ConstClass::MAX_VALUE;
    private const NUM_DIGITS = ConstClass::NUM_DIGITS;
    private const NUM_FILLS = ConstClass::NUM_FILLS;


    public function halfChar($value)
    {
        // オプションnは全角数字を半角数字へ
        return mb_convert_kana(strval($value), 'n');
    }

    // 文字列内の半角全角の空白全部削除する関数
    public function emptyDel($value): string
    {
        return preg_replace("/( |　)/", "", strval($value));
    }

    public function emptyIndention($value)
    {
        return preg_replace("/(\r|\n|\r\n)/", "", $value);
    }

    // 住所の結合(半角)
    public function mergeAddr($pref, $city, $addr): string
    {
        $addr = self::halfChar($addr);
        $mergeAddress = array($pref, $city, $addr);
        return implode('', $mergeAddress);
    }

    // 郵便番号の結合(ハイフン有り、半角)
    public function mergePostal ($postalTop, $postalBottom): string
    {
        $postalTop = self::halfChar($postalTop);
        $postalBottom = self::halfChar($postalBottom);
        $postalCodes = array($postalTop, $postalBottom);
        return implode('', $postalCodes);
    }


    // code生成関数
    public function makeMyCode(): string
    {
        $code = random_int(self::MIN_VALUE, self::MAX_VALUE);
        $code = strval($code);
        $code = str_pad($code, self::NUM_DIGITS, self::NUM_FILLS, STR_PAD_LEFT);
        return $code;
    }

    // パスワードのハッシュ化
    public function makeMyHash($password): string
    {
        $password = password_hash(strval($password), PASSWORD_DEFAULT);
        return $password;
    }


    public function join($value1, $value2)
    {
        $values = [$value1, $value2];
        return implode('', $values);
    }

    public function digest($value)
    {
        if ($value === null)
        {
            return;
        } else {
            return mb_strimwidth($value, 0, 30, '...');
        }
    }

}