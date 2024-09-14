<?php declare(strict_types = 1);

class ConstClass
{
    // SignUp.php
    const EMPTY_ARRAY = null;
    const SUCCESS_VALUE =true;

    // Make.php
    const MIN_VALUE = 1;
    const MAX_VALUE = 999999;
    const NUM_DIGITS = 6;
    const NUM_FILLS = '0';

    // Check.php
    const RESULT_TRUE = 1;
    const MIN_LENGTH = 8;
    const MAX_LENGTH = 20;
    const PREFECTURE = '静岡県';
    const CITY1 = '御前崎市';
    const CITY2 = '菊川市';
    const CITYS = [self::CITY1, self::CITY2];
    const MIME1 = 'image/jpg';
    const MIME2 = 'image/jpeg';
    const MIME3 = 'image/png';
    const MIME4 = 'image/gif';
    const MIMES = [self::MIME1, self::MIME2, self::MIME3, self::MIME4];

    // 3000000B = 3000KB = 3MB
    const MAX_FILE_SIZE = '3000000';

    // Message.php
    const INPUT_REQUIRED = '入力必須です';
    const INPUT_ALL_REQUIRED = '全ての入力欄は入力必須です';
    const NOT_USE_ALL_ENCODING = '全ての入力欄はエンコーディグ(UTF-8)にしてから入力して下さい';
    const NOT_USE_ENCODING = 'エンコーディグ(UTF-8)にしてから入力して下さい';
    const NOT_MODEL_NAME_ALL = '全ての入力欄は型名が違います';
    const NOT_MODEL_NAME = '型名が違います';
    const CHARACTERS_NOT_USED = '30文字以内で入力してください。改行やタブな・記号は使用できません';
    const INPUT_MISS = '記入内容が正しくありません';
    const OUT_OF_RANGE = '運搬範囲外の住所です';
    const VIOLATION_CHAR = '半角英字と半角数字の両方を組み合わせて下さい';
    const EXCESS_OR_DEFICIENCY_CHAR = '文字数は8文字以上20文字以下にして下さい';
    const USE_NOT_JOE_ACCOUNT = 'ユーザーIDとパスワードは同じにしないで下さい';
    const CAN_NOT_USER_ID = '入力されたユーザーIDは使用できません';
    const CAN_NOT_TEL = '入力された電話番号は使用できません';
    const MISTAKE_USER_ID_PASSWORD = '入力に誤りがあります';
    const NOT_INTEGER_ONLY = '正数のみ記入して下さい';
    const IMPOSSIBLE_EXTENTION = '拡張子が違います';
    const IMPOSSIBLE_FILE_SIZE = 'ファイルサイズが大きいです';
    const FILE_ERROR = 'ファイルのアップロードに失敗しました';
    const DUPLICATION_NAME = '商品名が重複しています';
    const NO_ITEM = '商品を選択していません';
    const UNAUTHORIZED_ACCESS = '不正なアクセスを検知しました';
    const NOT_DELIVERY = '配達日付を指定して下さい';
    const FUNNY_DAY = '配達日付に誤りがあります';
    const COUNT_ERROR = '不正な値を検知しました';
    const CALC_MISS = '計算を間違えています';
    const NOT_DELETE = '購入削除できませんでした';
    const SUCCESS_DELETE = '購入削除できました';
    const LACK_POINT = 'ポイント不足です';

    // DeliveryDate.php
    const CITY1_DAY = '金曜日';
    const CITY2_DAY = '土曜日';
    const CITY3_DAY = '日曜日';

    // MenuMoney.php & MenuPoint.php
    const DIVISION_NUM = 100;
    const MULTIPLY_NUM = 2;

    //
    const NONE_MENU = 0;
}