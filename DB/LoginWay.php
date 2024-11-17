<?php declare(strict_types=1);
require_once(__DIR__. '/PdoForm.php');
require_once(__DIR__. '/../DB/UserModel.php');
require_once(__DIR__. '/../class/Login.php');


class LoginWay{

    use PdoForm;

    public $currentYear;
    public $currentMonth;
    public $currentDay;
    public $nowDate;
    public $nowDateTime;
    public $oneYearAgoYear;
    public $oneYearAgo;
    private $threeMonthsLaterDateTime;
    private $oneWeekLaterDateTimeStamp;
    private $IPaddress;
    private $url;

    public function __construct($IPaddress, $url)
    {
        $objDateTime = new DateTime('now');
        $this->currentYear = $objDateTime->format('Y');
        $this->currentMonth = $objDateTime->format('m');
        $this->currentDay = $objDateTime->format('d');
        $this->nowDate = $objDateTime->format('Y-m-d');
        $this->nowDateTime = $objDateTime->format('Y-m-d H:i:s');
        $this->oneYearAgoYear = $objDateTime->modify('-1 year')->format('Y');
        $this->oneYearAgo = $objDateTime->format('Y-m-d');
        $this->threeMonthsLaterDateTime = $objDateTime->modify('+1 year')->modify('+3 month')->format('Y-m-d');
        $this->oneWeekLaterDateTimeStamp = $objDateTime->modify('-3 month')->modify('+1 week')->format('U');
        $this->IPaddress = $IPaddress;
        $this->url = $url;
    }


    public function getCurrentYear()
    {
        return $this->currentYear;
    }
    public function getCurrentMonth()
    {
        return $this->currentMonth;
    }
    public function getCurrentDay()
    {
        return $this->currentDay;
    }
    public function getCurrentDate()
    {
        return $this->nowDate;
    }
    public function getCurrentDateTime()
    {
        return $this->nowDateTime;
    }
    public function getOneYearAgoYear()
    {
        return $this->oneYearAgoYear;
    }
    public function getOneYearAgo()
    {
        return $this->oneYearAgo;
    }
    public function getThreeMonthsLater()
    {
        return $this->threeMonthsLaterDateTime;
    }
    public function getOneWeekLaterDateTimeStamp()
    {
        return $this->oneWeekLaterDateTimeStamp;
    }

    public function logout(){
        // userNameを平分で取得
        $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
        $userName = $userModel->selectName()->getName();
        // $logoutMessage = 'ログアウトしました';
        // token_infoテーブル内からuser_idにHITする全てのレコードを削除する
        $this->deleteUserIdTokenTable($_SESSION[ConstApp::SIGNUP_USER_ID]);
        // $_SESSIONと$_COOKIE値を削除
        $this->destroyCookieAndSession();
        // ログアウトのログを記載
        // file_put_contents(__DIR__. '/../errorLog/logoutRecord.php', $this->getCurrentDateTime(). ', IPアドレス '. $this->IPaddress. ', URL '. $this->url. ', ユーザー名 '. $userName. $logoutMessage. "\n", FILE_APPEND | LOCK_EX);
        // cookieに一時的に「ログアウトしました」保存する
        // setcookie('logout_message', $logoutMessage, time() + 10, '/');
        header('Location: index.php');
        exit();
    }

    public function autoLogin(){
        $token_id = $_COOKIE['token'];
        // token_idに不適切な文字がないか
        if (preg_match('/\A[0-9a-zA-Z]\z/', $token_id) !== 1){
            $this->errorCookie($token_id);
        } else{
            // token_idがtoken_infoテーブル内にて、存在且つ期限内であるか
            $row = $this->serchTokenTable($token_id);
            if (!isset($row)){
                // token_idが無いまたは、期限外である
                $this->errorCookie($token_id);
            } else{
                // token_id値が正しい
                // $_SESSIONの値を変更
                session_regenerate_id(true);
                // token_idと一致したuser_idを$_SESSIONへ代入
                $_SESSION[ConstApp::SIGNUP_USER_ID] = $row['user_id'];
                // 新規$_COOKIE
                $this->setLoginToken($row['user_id']);
                header('Location: index.php');
                exit();
            }
        }
    }



    public function login($datas)
    {
        $loginChecking = new Login($datas);
        $loginChecking->deleteEmpty();
        $loginChecking->checkInput();
        $loginChecking->messageInput();
        $_SESSION[ConstApp::SIGNUP_DATA] = $loginChecking->getDatas();
        $_SESSION[ConstApp::SIGNUP_MESSAGE] = $loginChecking->getMessage();
        if (!empty($loginChecking->getMessage())){
            return;
        } else{
            // セッション内を空にし、ログイン状態を判断するuser_idをセッションへ代入
            $_SESSION = [];
            $_SESSION[ConstApp::SIGNUP_USER_ID] = $loginChecking->getUserId();
            // $_SESSION[ConstApp::LOGIN_MESSAGE] = 'ログインしました';
            $loginMessage = 'ログインしました';
            setcookie('login_message', $loginMessage, time() + 10, '/');
            session_regenerate_id(true);
            // 新規$_COOKIE
            $this->setLoginToken($loginChecking->getUserId());
            header('Location: index.php');
            exit();
        }
    }

    public function firstLogin($userId){
        // セッション内を空にし、ログイン状態を判断するuser_idをセッションへ代入
        $_SESSION = [];
        $_SESSION[ConstApp::SIGNUP_USER_ID] = $userId;
        // $_SESSION[ConstApp::LOGIN_MESSAGE] = 'ログインしました';
        $loginMessage = 'ログインしました';
        setcookie('login_message', $loginMessage, time() + 10, '/');
        session_regenerate_id(true);
        // 新規$_COOKIE
        $this->setLoginToken($userId);
        return;
    }


    private function errorCookie($token_id){
        setcookie($token_id, '', time() - 600);
        header('Location: index.php');
        return;
    }


    public function destroyCookieAndSession(){
        setcookie('token_id', '', time() -60 * 60, '/', '', false, true);
        $_SESSION = array();
        if(ini_get('session.use_cookies')){
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        return;
    }

    private function serchTokenTable($token_id){
        $pdo = self::connect();
        $st = $pdo->prepare("SELECT * FROM token_info WHERE token_id = :token_id");
        $st->bindValue(':token_id', $token_id, PDO::PARAM_INT);
        $st->execute();
        while ($row = $st->fetch(PDO::FETCH_ASSOC)){
            if (strtotime($row['expires']) <= time()){
                $this->deleteTokenIdTokenTable($token_id);
                return null;
            }else {
                return $row;
            }
        }
        return null;
    }

    private function deleteTokenIdTokenTable($token_id){
        $pdo = self::connect();
        $st = $pdo->prepare("DELETE FROM token_info WHERE token_id = :token_id");
        $st->bindValue(':token_id', $token_id, PDO::PARAM_STR);
        $st->execute();
    }

    private function deleteUserIdTokenTable($user_id){
        $pdo = self::connect();
        $st = $pdo->prepare("DELETE FROM token_info WHERE user_id = :user_id");
        $st->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $st->execute();
    }

    // 新規$_COOKIE値を取得
    private function setLoginToken($user_id){
        try{
            $pdo = self::connect();
            $pdo->beginTransaction();
            // token_infoテーブル内からuserの持つ$_COOKIE値を削除
            if (isset($_COOKIE['token'])){
                $this->deleteTokenIdTokenTable($_COOKIE['token']);
            }

            // 疑似乱数を生成しtoken_inifoテーブル内に同一のtoken_idが存在するか
            $st = $pdo->prepare("SELECT * FROM token_info WHERE token_id = :token_id");
            for ($i = 0; $i < 10; $i++){
                $generation_token_id = bin2hex(random_bytes(32));
                $st->bindValue(':token_id', $generation_token_id, PDO::PARAM_STR);
                $st->execute();
                if (!$row = $st->fetch(PDO::FETCH_ASSOC)){
                    // 同一のtoken_idが無いため、$newtoken_idに代入しbreak処理で抜ける
                    $newtoken_id = $generation_token_id;
                    break;
                }
            }

            // token_info内に重複のない新規のnewtoken_idが存在しなければエラーをスローする
            if (empty($newtoken_id)){
                throw new Exception('cookieTokenGenerationError');
            }


            // token_infoテーブルへinsertする
            $st = $pdo->prepare(
                "INSERT INTO token_info (token_id, user_id, expires, created_at)
                VALUES (:token_id, :user_id, :expires, CURRENT_TIMESTAMP)"
            );
            $st->bindValue(':token_id', $newtoken_id, PDO::PARAM_STR);
            $st->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $st->bindValue(':expires', $this->getThreeMonthsLater(), PDO::PARAM_STR);
            $st->execute();
            $pdo->commit();
        } catch(PDOException $e){
            // userNameを平分で取得
            $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
            $userName = $userModel->selectName()->getName();
            // 処理内にエラーが発生した場合にcatchし、エラーログに追記・error.phpへ遷移
            file_put_contents(__DIR__. '/../errorLog/DBError.php', $this->getCurrentDateTime(). ', IPアドレス '. $this->IPaddress. ', URL '. $this->url. ', ユーザー名 '. $userName. ', 異常名 '. $e->getLine(). $e->getMessage(). "\n", FILE_APPEND | LOCK_EX);
            $pdo->rollBack();
            header('Location: error.php');
            exit();
        } catch(Exception $e){
            // userNameを平分で取得
            $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
            $userName = $userModel->selectName()->getName();
            // $newtoken_idが存在しない場合にcatchし、エラーログに追記・error.phpへ遷移
            file_put_contents(__DIR__. '/../errorLog/tokenError.php', $this->getCurrentDateTime(). ', IPアドレス '. $this->IPaddress. ', URL '. $this->url. ', ユーザー名 '. $userName. ', 異常名 '. $e->getLine(). $e->getMessage(). "\n", FILE_APPEND | LOCK_EX);
            $pdo->rollBack();
            header('Location: error.php');
            exit();
        }
        // ログインのログを記載
        $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
        $userName = $userModel->selectName()->getName();
        file_put_contents(__DIR__. '/../errorLog/loginRecord.php', $this->getCurrentDateTime(). ', IPアドレス '. $this->IPaddress. ', URL '. $this->url. ', ユーザー名 '. $userName. 'ログインしました'. "\n", FILE_APPEND | LOCK_EX);
        // $_COOKIEを発行
        setcookie('token_id', $newtoken_id, time() + 60 * 60 * 24 * 30, '/');
        return;
    }
}