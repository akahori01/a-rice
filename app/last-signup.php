<?php declare(strict_types = 1);
header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
session_cache_limiter('nocache');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
require_once(__DIR__. '/../DB/LoginWay.php');
require_once(__DIR__. '/../DB/UserModel.php');
require_once(__DIR__. '/../class/SignUp.php');
require_once(__DIR__. '/../configs/constApp.php');
require_once(__DIR__. '/../configs/constDB.php');
session_start();


$url = empty($_SERVER['HTTPS']) ? 'http://' : 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$IPaddress = $_SERVER['REMOTE_ADDR'];
$howToLogin = new LoginWay($IPaddress, $url);
if (isset($_POST['logout']) && isset($_SESSION['logout']) && $_POST['logout'] === $_SESSION['logout']){
    $howToLogin->logout();
} elseif (isset($_COOKIE['token']) && !isset($_SESSION['user_id'])){
    $howToLogin->autologin();
} elseif(isset($_SESSION['user_id'])){
    $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
    $fairSessionId = $userModel->checkUserId();
    if($fairSessionId){
        header('Location: index.php');
        exit();
    } else{
        $howToLogin->destroyCookieAndSession();
        header('Location: error.php');
        exit();
    }
}

if (!isset($_POST['edit_signup_token']) || $_SESSION['edit_signup_token'] !== $_POST['edit_signup_token']) {
    header('Location: first-signup.php');
    exit();
}

const KEY_URL = ConstApp::KEY_URL;
if (!file_exists(KEY_URL)){
    touch(KEY_URL);
    $key = openssl_random_pseudo_bytes(32);
    file_put_contents(KEY_URL, $key);
}

$signUp = new SignUp($_SESSION[ConstApp::SIGNUP_DATA]);
$signUp->writeSignupDate($IPaddress, $url);
$_SESSION[ConstApp::SIGNUP_DATA] = $signUp->getDatas();
$_SESSION[ConstApp::SIGNUP_MESSAGE] = $signUp->getMessage();
if (!empty($signUp->getMessage())){
    header('Location: ../app/first-signup.php');
    exit();
}

$howToLogin->firstLogin($signUp->setUserId());
header('Location: index.php');
exit();