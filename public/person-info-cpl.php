<?php declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
session_cache_limiter('nocache');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
require_once(__DIR__. '/../DB/LoginWay.php');
require_once(__DIR__. '/../DB/UserModel.php');

require_once(__DIR__. '/../configs/constApp.php');
require_once(__DIR__. '/../class/SignUp.php');
session_start();


$url = empty($_SERVER['HTTPS']) ? 'http://' : 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$IPaddress = $_SERVER['REMOTE_ADDR'];
$howToLogin = new LoginWay($IPaddress, $url);

if (isset($_POST['logout']) && isset($_SESSION['logout']) && $_POST['logout'] === $_SESSION['logout']){
    $howToLogin->logout();
} elseif (isset($_COOKIE['token']) && !isset($_SESSION[ConstApp::SIGNUP_USER_ID])){
    $howToLogin->autologin();
} elseif(isset($_SESSION[ConstApp::SIGNUP_USER_ID])){
    $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
    $fairSessionId = $userModel->checkUserId();
    if(!$fairSessionId){
        $howToLogin->destroyCookieAndSession();
        header('Location: error.php');
        exit();
    }
}else {
    header('Location: index.php');
    exit();
}




if (!isset($_POST['person_cfm_token']) || $_SESSION['person_cfm_token'] !== $_POST['person_cfm_token']) {
    header('Location: mypage.php');
    exit();
}

$tel_on_data = $userModel->selectTel()->getTel();
$user_id_on_data = $userModel->selectUserId()->getUserId();
$post_password = $_POST['password'];
$password_on_data = null;
if (empty($post_password)){
    $datas = [
        ConstApp::SIGNUP_NAME => $_POST['name'],
        ConstApp::SIGNUP_PREF => $_POST['pref31'],
        ConstApp::SIGNUP_CITY => $_POST['addr31'],
        ConstApp::SIGNUP_ADDR => $_POST['strt31'],
        ConstApp::SIGNUP_POSTAL_TOP => $_POST['zip31'],
        ConstApp::SIGNUP_POSTAL_BOTTOM => $_POST['zip32'],
        ConstApp::SIGNUP_TEL => $_POST['tel'],
        ConstApp::SIGNUP_USER_ID => $_POST['userId'],
    ];
    $password_on_data = $userModel->selectPassword(false)->getPassword()[ConstApp::SIGNUP_PASSWORD];
}else {
    $datas = [
        ConstApp::SIGNUP_NAME => $_POST['name'],
        ConstApp::SIGNUP_PREF => $_POST['pref31'],
        ConstApp::SIGNUP_CITY => $_POST['addr31'],
        ConstApp::SIGNUP_ADDR => $_POST['strt31'],
        ConstApp::SIGNUP_POSTAL_TOP => $_POST['zip31'],
        ConstApp::SIGNUP_POSTAL_BOTTOM => $_POST['zip32'],
        ConstApp::SIGNUP_TEL => $_POST['tel'],
        ConstApp::SIGNUP_USER_ID => $_POST['userId'],
        ConstApp::SIGNUP_PASSWORD => $_POST['password']
    ];
}




$signup = new SignUp($datas);
$signup->signupCheck($tel_on_data, $user_id_on_data);
$_SESSION[ConstApp::PERSON_DATA] = $signup->getDatas();
$_SESSION[ConstApp::PERSON_MESSAGE] = $signup->getMessage();
if (!empty($signup->getMessage())){
    header('Location: ./person-info-cfm.php');
    exit();
}
$userModel->selectId();
$id_on_data = strval($userModel->getId());
$signup->writeSignupDate($IPaddress, $url, $password_on_data, $id_on_data, $tel_on_data, $user_id_on_data);

$howToLogin->firstLogin($signup->setUserId());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/person-info-cpl.css">
    <link rel="stylesheet" href="./assets/notLoginHeader.css">
    <link rel="stylesheet" href="./assets/loginHeader.css">
    <link rel="stylesheet" href="./assets/footer.css">
    <link rel="stylesheet" href="./assets/same.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/library/apple-touch-icon.png"> <!-- iOS専用 -->
    <link rel="manifest" href="/site.webmanifest"> <!-- PWA用 -->
    <title>個人情報変更完了画面</title>
</head>
<body>
    <?php if (isset($_SESSION[ConstApp::SIGNUP_USER_ID])): ?>
        <header><?php require_once(__DIR__. '/../inc/loginHeader.php') ?></header>
    <?php else: ?>
        <header><?php require_once(__DIR__. '/../inc/notLoginHeader.php') ?></header>
    <?php endif ?>
    <main>
        <div class="title">
            <h1>赤堀産地の<br>コシヒカリ</h1>
        </div>
        <div class="subheading">
            <h2>個人情報の変更完了</h2>
        </div>
        <p>変更完了しました</p>
        <div class="move">
            <a href="mypage.php">マイページへ</a>
        </div>
    </main>
    <?php if (isset($_SESSION[ConstApp::SIGNUP_USER_ID])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
</body>
</html>