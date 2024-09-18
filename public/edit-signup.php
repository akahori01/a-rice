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
} elseif (isset($_COOKIE['token']) && !isset($_SESSION[ConstApp::SIGNUP_USER_ID])){
    $howToLogin->autologin();
} elseif(isset($_SESSION[ConstApp::SIGNUP_USER_ID])){
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


if (!isset($_POST['first_signup_token']) || $_SESSION['first_signup_token'] !== $_POST['first_signup_token']) {
    header('Location: first-signup.php');
    exit();
}

$datas = array(
    ConstApp::SIGNUP_NAME => $_POST['name'],
    ConstApp::SIGNUP_PREF => $_POST['pref31'],
    ConstApp::SIGNUP_CITY => $_POST['addr31'],
    ConstApp::SIGNUP_ADDR => $_POST['strt31'],
    ConstApp::SIGNUP_POSTAL_TOP => $_POST['zip31'],
    ConstApp::SIGNUP_POSTAL_BOTTOM => $_POST['zip32'],
    ConstApp::SIGNUP_TEL => $_POST['tel'],
    ConstApp::SIGNUP_USER_ID => $_POST['userId'],
    ConstApp::SIGNUP_PASSWORD => $_POST['password']
);
$signUp = new SignUp($datas);
$signUp->signupCheck();
$_SESSION[ConstApp::SIGNUP_DATA] = $signUp->getDatas();
$_SESSION[ConstApp::SIGNUP_MESSAGE] = $signUp->getMessage();


if (!empty($signUp->getMessage())){
    header('Location: ../public/first-signup.php');
    exit();
}

$randomId = bin2hex(random_bytes(32));
$_SESSION['edit_signup_token'] = $randomId;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
    // ダブルクリックによる2重送信防止
    $(function(){
        $('button[type="submit"]').on('click', function(){
            $(this).prop('disabled', true);
            $(this).closest('form').submit();
        });
    });
    </script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/signUp.css">
    <title>新規登録確認画面</title>
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
        <div class="subheading">
            <h2>お間違いがないか<br>確認して下さい</h2>
        </div>
        <div class="top-wrapper">
            <div class="container">
                <ul>
                    <li>名前(全角)</li>
                    <li class="under-line"><?= $signUp->getDatas()['name'] ?></li>
                </ul>
            </div>
            <div class="container">
                <ul>
                    <li>郵便番号</li>
                    <li class="under-line"><?= $signUp->getPostalCode() ?></li>
                </ul>
            </div>
            <div class="container">
                <ul>
                    <li>住所</li>
                    <li class="under-line"><?= $signUp->getDatas()['address'] ?></li>
                </ul>
            </div>
            <div class="container">
                <ul>
                    <li>携帯電話番号</li>
                    <li class="under-line"><?= $signUp->getTel() ?></li>
                </ul>
            </div>
            <div class="container">
                <ul>
                    <li>ユーザーID<br>(次回ログイン時に必要です)</li>
                    <li class="under-line"><?= $signUp->getDatas()['user_id'] ?></li>
                </ul>
            </div>
            <div class="container">
                <ul>
                    <li>パスワード<br>(次回ログイン時に必要です)</li>
                    <li class="under-line"><?= $signUp->getDatas()['password'] ?></li>
                </ul>
            </div>
        </div>
        <div class="edit-forms">
            <form action="first-signup.php">
                <button class="left" type="submit">戻る</button>
            </form>
            <form action="last-signup.php" method="post">
                <button class="right" type="subimt" name="edit_signup_token" value="<?= $randomId ?>">登録完了</button>
            </form>
        </div>
        <div class="not-double-click">
            <p>ワンクリック<br>押し</p>
        </div>
    </main>
    <?php if (isset($_SESSION['user_id'])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
</body>
</html>
