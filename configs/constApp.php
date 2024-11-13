<?php declare(strict_types = 1);

class ConstApp
{

    // SignUp.php & Insert.php も含む
    const POSTAL_TOP_DIGITS = '3';
    const POSTAL_BOTTOM_DIGITS = '4';
    const NOT_COUPON = 0;
    const SIGNUP_USER_ID = 'user_id';
    const SIGNUP_NAME = 'name';
    const SIGNUP_ADDRESS = 'address';
    const SIGNUP_POSTAL_CODE = 'postalCode';
    const SIGNUP_PREF = 'pref';
    const SIGNUP_CITY = 'city';
    const SIGNUP_ADDR = 'addr';
    const SIGNUP_POSTAL_TOP = 'postalTop';
    const SIGNUP_POSTAL_BOTTOM = 'postalBottom';
    const SIGNUP_TEL = 'tel';
    const SIGNUP_PASSWORD = 'password';
    const SIGNUP_CODE = 'code';
    const SIGNUP_ALL_MESSAGE ='all_message';
    const SIGNUP_ENCODING ='encoding';

    const SIGNUP_USER_ID_IV = 'user_idIv';
    const SIGNUP_NAME_IV = 'nameIv';
    const SIGNUP_ADDRESS_IV = 'addressIv';
    const SIGNUP_POSTAL_CODE_IV = 'postalCodeIv';
    const SIGNUP_TEL_IV = 'telIv';
    const SIGNUP_PASSWORD_HASH = 'passwordHash';

    const SIGNUP_DATA = 'data';
    const SIGNUP_MESSAGE = 'message';
    const SIGNUP_SECRET = 'secret';

    const COUPON_PRICE = 1000;

    const KEY_URL = '../admin/donot-touch/storage.key';

    // order-cfm.php
    const ORDER = 'order';
    const ORDER_MESSAGE = 'orderMessage';

    // Menu.php
    const BUSINESS_SET = 'business_set';
    const MENU_IMAGE = 'menu_image';
    const MENU_NAME = 'menu_name';
    const MENU_COST = 'menu_cost';
    const MENU_WEIGHT = 'menu_weight';
    const MENU_CATEGORY = 'menu_category';
    const MENU_OTHER = 'other';
    const MENU_UNIT = 'menu_unit';
    const MENU_COMMENT_TOP = 'comment_top';
    const MENU_COMMENT_BOTTOM = 'comment_bottom';
    const MENU_NOTES = 'notes';
    const TOTAL_COMMENT = 'totalComment';
    const DIGEST_COMMENT = 'digestComment';
    const DIGEST_NOTES = 'digestNotes';
    const MENU_MONEY_DATA = 'moneyData';
    const MENU_POINT_DATA = 'pointData';
    const MENU_MONEY_MESSAGE = 'moneyMessage';
    const MENU_POINT_MESSAGE = 'pointMessage';
    const MENU_MONEY_MESSAGE_IMAGE = 'moneyMessageImage';
    const MENU_POINT_MESSAGE_IMAGE = 'pointMessageImage';
    const FILE_NAME = 'name';
    const FILE_TYPE = 'type';
    const FILE_TMP_NAME = 'tmp_name';
    const FILE_ERROR = 'error';
    const FILE_SIZE = 'size';
    const RICE = '米';
    const VEGETABLE = '野菜';
    const SWEET = 'スイート';
    const OTHERS = 'その他';
    const BUSINESS_MONEY = 0;
    const BUSINESS_POINT = 1;
    const MENU_KG = 'kg';
    const MENU_G = 'g';
    const MENU_NUM = '個';
    const MENU_STALK = '本';
    const MENU_BUNCH = '房';
    const MENU_HEAD = '玉';
    const DEFAULT_IMAGE_NAME = 'MyLogo.jpeg';
    const DEFAULT_IMAGE_TYPE = 'image/jpeg';
    const DEFAULT_IMAGE_PASS = '../../../library/MyLogo.jpeg';
    const DEFAULT_IMAGE_SIZE = 5800;

    // Calendar.php
    const CALENDAR = 'calendar';
    const CALENDAR_DATA = 'calendarData';
    const CALENDAR_MESSAGE = 'calendarMessage';

    // Mypage.php
    const MYPAGE = 'mypage';

    // index.php
    const LOGIN_MESSAGE = 'login_message';

    // person-info.php
    const PERSON_DATA = 'personData';
    const PERSON_MESSAGE = 'personMessage';

    // company.php  largelot-index.php
    const PRICE_RICE_5 = 1900;
    const PRICE_RICE_10 = 3800;
    const PRICE_RICE_15 = 5500;
    const PRICE_RICE_30 = 11000;

    // menu-detail.php
    const LARGELOT_ORIGINAL_MANU_ID = 1;
}