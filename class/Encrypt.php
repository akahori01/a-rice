<?php declare(strict_types = 1);
class Encrypt
{
    private const DEFAULT_METHOD = ConstDB::DEFAULT_METHOD;
    private $key;

    public function __construct()
    {
        $this->key = file_get_contents(__DIR__. '/../admin/donot-touch/storage.key');
    }

    public function generationIv()
    {
        $ivLength = openssl_cipher_iv_length(self::DEFAULT_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);
        return $iv;
    }

    // 暗号化
    public function crypt($value): array
    {
        $iv = $this->generationIv();
        $encryptValue = openssl_encrypt($value, self::DEFAULT_METHOD, $this->key, OPENSSL_RAW_DATA, $iv);
        return [bin2hex($encryptValue), bin2hex($iv)];
    }

    public function matchEncrypt($value, $arrayIdIv)
    {
        for ($i = 0; $i < count($arrayIdIv); $i++){
            list($id, $iv) = array_keys($arrayIdIv[$i]);
            $id = $arrayIdIv[$i][$id];
            $iv = $arrayIdIv[$i][$iv];
            $encryptValue = openssl_encrypt(strval($value), self::DEFAULT_METHOD, $this->key, OPENSSL_RAW_DATA, strval(hex2bin($iv)));
            $encryptValue = bin2hex($encryptValue);
            if ($encryptValue === $id){
                return $encryptValue;
            }
        }
        $encryptValue = null;
        return $encryptValue;
    }
    // 連想配列を複合化
    public function composite(array $array)
    {
        list($column, $columnIv) = array_keys($array);
        $column = $array[$column];
        $columnIv = $array[$columnIv];
        $decryption = openssl_decrypt(strval(hex2bin($column)), self::DEFAULT_METHOD, $this->key, OPENSSL_RAW_DATA, strval(hex2bin($columnIv)));
        return $decryption;
    }

}