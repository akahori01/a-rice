<?php declare(strict_types = 1);

class ConstDB
{
    const DEFAULT_METHOD = 'AES-256-CBC';
    const DB_COLUMN_ID = 'id';
    const DB_COLUMN_USER_ID = 'user_id';
    const DB_COLUMN_USER_ID_IV = 'user_id_iv';
    const DB_COLUMN_NAME = 'name';
    const DB_COLUMN_NAME_IV = 'name_iv';
    const DB_COLUMN_CITY = 'city';
    const DB_COLUMN_CITY_IV = 'city_iv';
    const DB_COLUMN_ADDRESS = 'address';
    const DB_COLUMN_ADDRESS_IV = 'address_iv';
    const DB_COLUMN_POSTAL_CODE = 'postal_code';
    const DB_COLUMN_POSTAL_CODE_IV = 'postal_code_iv';
    const DB_COLUMN_TEL = 'tel';
    const DB_COLUMN_TEL_IV = 'tel_iv';
    const DB_COLUMN_PASSWORD = 'password';

    const DB_COLUMN_MENU_ID = 'menu_id';
    const DB_COLUMN_TOTAL_COST = 'total_cost';
    const DB_COLUMN_ORDER_COUNT = 'order_count';
    const DB_COLUMN_ADD_POINT = 'add_point';

    // UserModel.php
    const ADMIN_USER_ID = 'admin12rice23';
}
