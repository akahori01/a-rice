<?php declare(strict_types = 1);
header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
session_cache_limiter('nocache');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
require_once(__DIR__. '/../configs/constApp.php');
require_once(__DIR__. '/../DB/LoginWay.php');
require_once(__DIR__. '/../class/Login.php');
session_start();

$url = empty($_SERVER['HTTPS']) ? 'http://' : 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$IPaddress = $_SERVER['REMOTE_ADDR'];
$howToLogin = new LoginWay($IPaddress, $url);
if (isset($_POST['logout']) && isset($_SESSION['logout']) && $_POST['logout'] === $_SESSION['logout']){
    $howToLogin->logout();
} elseif (isset($_COOKIE['token']) && !isset($_SESSION['user_id'])){
    $howToLogin->autologin();
} elseif (isset($_POST['login-send']) && $_POST['login-send'] === 'login') {
    $datas = [
        ConstApp::SIGNUP_USER_ID => $_POST['userId'],
        ConstApp::SIGNUP_PASSWORD => $_POST['password']
    ];
    $howToLogin->login($datas);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/login.css">
    <title>登録者済み専用ログインページ</title>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <header><?php require_once(__DIR__. '/../inc/loginHeader.php') ?></header>
    <?php else: ?>
        <header><?php require_once(__DIR__. '/../inc/notLoginHeader.php') ?></header>
    <?php endif ?>
    <main>
        <div class="title">
            <h1>赤堀産地の<br>コシヒカリ</h1>
        </div>
        <div class="top-wrapper">
            <div class="subheading">
                <h2>ログイン</h2>
            </div>
            <div class="login-form">
                <form action="#" method="post">
                    <div>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::LOGIN_MESSAGE]) ? $_SESSION[ConstApp::LOGIN_MESSAGE] : '' ?></p>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::ORDER]) ? $_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::ORDER] : '' ?></p>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::CALENDAR]) ? $_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::CALENDAR] : '' ?></p>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::SIGNUP_PASSWORD]) ? $_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::SIGNUP_PASSWORD] : '' ?></p>
                    </div>
                    <ul><li>ユーザーID<span class="blank"></span>又は<span class="indention"></span>携帯電話番号(ハイフン無し)</li></ul>
                    <div><input type="text" name="userId" autocorrect="off" autocapitalize="off" value="<?= isset($_SESSION[ConstApp::SIGNUP_DATA][ConstApp::SIGNUP_USER_ID]) ? $_SESSION[ConstApp::SIGNUP_DATA][ConstApp::SIGNUP_USER_ID] : '' ?>" required></div>
                    <ul><li>パスワード</li></ul>
                    <div><input type="password" name="password" autocorrect="off" autocapitalize="off" value="<?= isset($_SESSION[ConstApp::SIGNUP_DATA][ConstApp::SIGNUP_PASSWORD]) ? $_SESSION[ConstApp::SIGNUP_DATA][ConstApp::SIGNUP_PASSWORD] : '' ?>" required></div>
                    <button type="submit" name="login-send" value="login">ログインする</button>
                </form>
            </div>
            <a href="first-signup.php">新規登録者はこちらから</a>
        </div>
    </main>
    <?php if (isset($_SESSION['user_id'])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
    <?php $_SESSION[ConstApp::SIGNUP_MESSAGE] = [] ?>
    <?php $_SESSION[ConstApp::SIGNUP_DATA] = [] ?>
    <?php $_SESSION[ConstApp::LOGIN_MESSAGE] = '' ?>
</body>
</html>

<?php
