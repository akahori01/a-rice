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
    if(!$fairSessionId){
        $howToLogin->destroyCookieAndSession();
        header('Location: error.php');
        exit();
    }
}else {
    header('Location: index.php');
    exit();
}

/* ここに
if(SMS認証 === NG){
    header('Location: error.php');
}
*/


$name = $userModel->selectName()->getName();
list($postalcodeTop, $postalcodeBottom) = $userModel->selectPostalCode()->getPostalCode();
list($pref, $city, $addr) = $userModel->selectAddress()->getAddress();
$tel = $userModel->selectTel()->getTel();
$user_id = $userModel->selectUserId()->getUserId();


$randomId = bin2hex(random_bytes(32));
$_SESSION['person_cfm_token'] = $randomId;


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
    $(function(){
        $(window).on('beforeunload', function() {
            return '投稿が完了していません。このまま移動しますか？';
        });
        $("button").click(function() {
            $(window).off('beforeunload');
        });
    });
    </script>
    <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/person-info-cfm.css">
    <title>個人情報の変更</title>
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
            <h2>個人情報の変更</h2>
        </div>
        <div class="top-wrapper">
            <p><?= isset($_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_ALL_MESSAGE]) ? $_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_ALL_MESSAGE] : '' ?></p>
            <form action="./person-info-cpl.php" method="post">
                <div class="container">
                    <ul>
                        <li class="under-line">名前(全角)</li>
                        <li class="warning"><?= isset($_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_NAME]) ? $_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_NAME] : '' ?></li>
                        <li><input type="text" name="name" autocomplete="name" placeholder="田中太郎" maxlength="20" autocorrect="off" autocapitalize="off" value="<?= isset($_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_NAME]) ? $_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_NAME] : $name ?>" required></li>
                    </ul>
                </div>
                <div class="container">
                    <ul>
                        <li class="under-line">郵便番号</li>
                        <li class="warning"><?= isset($_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_POSTAL_CODE]) ? $_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_POSTAL_CODE] : '' ?></li>
                        <li class="inline"><input type="text" name="zip31" placeholder="437" size="4" maxlength="<?= ConstApp::POSTAL_TOP_DIGITS ?>" value="<?= isset($_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_POSTAL_TOP]) ? $_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_POSTAL_TOP] : $postalcodeTop ?>" required></li>
                        <li class="inline">-</li>
                        <li class="inline"><input type="text" name="zip32" placeholder="1612" size="5" maxlength="<?= ConstApp::POSTAL_BOTTOM_DIGITS ?>" value="<?= isset($_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_POSTAL_BOTTOM]) ? $_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_POSTAL_BOTTOM] : $postalcodeBottom ?>" onKeyUp="AjaxZip3.zip2addr('zip31','zip32','pref31','addr31','strt31');" required></li>
                    </ul>
                </div>
                <div class="container">
                    <ul>
                        <li class="under-line">住所</li>
                        <li class="warning"><?= isset($_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_ADDRESS]) ? $_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_ADDRESS] : '' ?></li>
                        <li class="inline">都道府県</li>
                        <li class="inline"><input type="text" name="pref31" autocomplete="address-level1" placeholder="静岡県" size="15" maxlength="3" value="<?= isset($_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_PREF]) ? $_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_PREF] : $pref ?>" required></li>
                        <span class="indention"></span>
                        <li class="inline">市区町村</li>
                        <li class="inline"><input type="text" name="addr31" autocomplete="address-level2" placeholder="御前崎市" size="15" maxlength="4" value="<?= isset($_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_CITY]) ? $_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_CITY] : $city ?>" required></li>
                        <span class="indention"></span>
                        <li class="inline">以降の番地</li>
                        <li class="inline"><input type="text" name="strt31" autocomplete="address-line1" placeholder="池新田0000-00" size="30" maxlength="40" value="<?= isset($_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_ADDR]) ? $_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_ADDR] : $addr ?>" required></li>
                    </ul>
                </div>
                <div class="container">
                    <ul>
                        <li class="under-line">携帯電話番号(ハイフン無し)</li>
                        <li class="warning"><?= isset($_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_TEL]) ? $_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_TEL] : '' ?></li>
                        <li><input type="tel" name="tel" size="15" placeholder="08012345678" autocorrect="off" autocapitalize="off" value="<?= isset($_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_TEL]) ? $_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_TEL] : $tel ?>" required></li>
                    </ul>
                </div>
                <div class="container">
                    <ul>
                        <li class="under-line">ユーザーID</li>
                        <li class="small-font">※次回ログイン時に必要です</li>
                        <li class="small-font">※半角英語と半角数字の両方を用い,<span class="indention"></span>記号無しの8文字以上20文字以下</li>
                        <li class="warning"><?= isset($_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_USER_ID]) ? $_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_USER_ID] : '' ?></li>
                        <li><input type="text" name="userId" size="30" autocorrect="off" autocapitalize="off" value="<?= isset($_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_USER_ID]) ? $_SESSION[ConstApp::PERSON_DATA][ConstApp::SIGNUP_USER_ID] : $user_id ?>" required></li>
                    </ul>
                </div>
                <div class="container">
                    <ul>
                        <li class="under-line">新しいパスワードへ変更したい方は入力</li>
                        <li class="small-font">※次回ログイン時に必要です</li>
                        <li class="small-font">※半角英語と半角数字の両方を用い,<span class="indention"></span>記号無しの8文字以上20文字以下</li>
                        <li class="warning"><?= isset($_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_PASSWORD]) ? $_SESSION[ConstApp::PERSON_MESSAGE][ConstApp::SIGNUP_PASSWORD] : '' ?></li>
                        <li><input type="password" name="password" size="30" autocorrect="off" autocapitalize="off""></li>
                    </ul>
                </div>
                <button type="submit" name="person_cfm_token" value="<?= $randomId ?>">送信する</button>
            </form>
        </div>
    </main>
    <?php if (isset($_SESSION[ConstApp::SIGNUP_USER_ID])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
    <?php $_SESSION[ConstApp::PERSON_DATA] = [] ?>
    <?php $_SESSION[ConstApp::PERSON_MESSAGE] = [] ?>
</body>
</html>