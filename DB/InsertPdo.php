<?php declare(strict_types = 1);

require_once(__DIR__. '/PdoForm.php');
require_once(__DIR__. '/../configs/constApp.php');

class InsertPdo
{
    private const SIGNUP_USER_ID = ConstApp::SIGNUP_USER_ID;
    private const SIGNUP_NAME = ConstApp::SIGNUP_NAME;
    private const SIGNUP_ADDRESS = ConstApp::SIGNUP_ADDRESS;
    private const SIGNUP_POSTAL_CODE = ConstApp::SIGNUP_POSTAL_CODE;
    private const SIGNUP_TEL = ConstApp::SIGNUP_TEL;
    private const SIGNUP_PASSWORD_HASH = ConstApp::SIGNUP_PASSWORD_HASH;
    private const SIGNUP_USER_ID_IV = ConstApp::SIGNUP_USER_ID_IV;
    private const SIGNUP_NAME_IV = ConstApp::SIGNUP_NAME_IV;
    private const SIGNUP_ADDRESS_IV = ConstApp::SIGNUP_ADDRESS_IV;
    private const SIGNUP_POSTAL_CODE_IV = ConstApp::SIGNUP_POSTAL_CODE_IV;
    private const SIGNUP_TEL_IV = ConstApp::SIGNUP_TEL_IV;
    private const SIGNUP_SECRET = ConstApp::SIGNUP_SECRET;

    use PdoForm;

    public function insertSignUp(array $datas, $IPaddress, $url)
    {
        $objDateTime = new DateTime('now');
        try{
            $pdo = self::connect();
            $pdo->beginTransaction();
            $statement = $pdo->prepare(
                "INSERT INTO user_info(user_id, name, address, postal_code, tel, created_at, updated_at, user_id_iv, name_iv, address_iv, postal_code_iv, tel_iv, password)
                VALUES(:userId, :name, :address, :postalCode, :tel, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :userIdIv, :nameIv, :addressIv, :postalCodeIv, :telIv, :passwordHash)"
                );
            foreach ($datas[self::SIGNUP_SECRET] as $key => $value)
            {
                switch ($key)
                {
                    case self::SIGNUP_USER_ID:
                        $statement->bindValue(':userId', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_USER_ID_IV:
                        $statement->bindValue(':userIdIv', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_NAME:
                        $statement->bindValue(':name', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_NAME_IV:
                        $statement->bindValue(':nameIv', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_ADDRESS:
                        $statement->bindValue(':address', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_ADDRESS_IV:
                        $statement->bindValue(':addressIv', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_POSTAL_CODE:
                        $statement->bindValue(':postalCode', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_POSTAL_CODE_IV:
                        $statement->bindValue(':postalCodeIv', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_TEL:
                        $statement->bindValue(':tel', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_TEL_IV:
                        $statement->bindValue(':telIv', $value, PDO::PARAM_STR);
                        break;
                    case self::SIGNUP_PASSWORD_HASH:
                        $statement->bindValue(':passwordHash', $value, PDO::PARAM_STR);
                        break;
                }
            }
            $statement->execute();
            $pdo->commit();
        }catch(PDOException $e){
            file_put_contents(__DIR__. '/../errorLog/DBError.php', $objDateTime->format('Y-m-d H:i:s'). ', IPアドレス '. $IPaddress. ', URL '. $url. ', 異常名 '. $e->getLine(). $e->getMessage(). "\n", FILE_APPEND | LOCK_EX);
            $pdo->rollBack();
            header('Location: error.php');
            exit();
        }
    }
}