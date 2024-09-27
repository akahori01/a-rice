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
}

$rice5 = number_format(ConstApp::PRICE_RICE_5). '円';
$rice10 = number_format(ConstApp::PRICE_RICE_10). '円';
$rice15 = number_format(ConstApp::PRICE_RICE_15). '円';
$rice30 = number_format(ConstApp::PRICE_RICE_30). '円';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/company.css">
    <link rel="stylesheet" href="./assets/notLoginHeader.css">
    <link rel="stylesheet" href="./assets/loginHeader.css">
    <link rel="stylesheet" href="./assets/footer.css">
    <link rel="stylesheet" href="./assets/same.css">
    <title>会社情報</title>
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
            <h2>会社情報</h2>
        </div>
        <div class="features">
            <h3>特徴</h3>
            <div class="top-wrapper">
                <div class="container">
                    <div class="image">
                        <p><img src="/library/rice-polishing.png" alt="精米"></p>
                    </div>
                    <div class="text">
                        <p>お客様からの注文が入ってから精米</p>
                    </div>
                </div>
                <div class="container">
                    <div class="image">
                        <p><img src="/library/rice-delivery.png" alt="配達"></p>
                    </div>
                    <div class="text">
                        <p>精米・配達は無料で行っております</p>
                    </div>
                </div>
                <div class="container">
                    <div class="image">
                        <p><img src="/library/ear-of-rice.png" alt="米ぬか説明"></p>
                    </div>
                    <div class="text">
                        <p>実量の約1割ほど米ぬかになります</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="special-case">
            <h3>一度に150kg以上の大口で注文された方のみ</h3>
            <div class="second-wrapper">
                <div class="container">
                    <div class="image">
                        <p><img src="/library/ric-bales.png" alt="米俵"></p>
                    </div>
                    <div class="text">
                        <p>15kg・30kgに加え、5kg・10kg単位と小分けでの注文が可能です</p>
                    </div>
                </div>
                <div class="container">
                    <div class="image">
                        <p><img src="/library/golf-championship.png" alt="米俵"></p>
                    </div>
                    <div class="text">
                        <p>ゴルフコンペの景品・ご贈答品などに如何でしょうか</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="price">
            <h3>各種価格</h3>
            <div class="third-wrapper">
                <div class="text">
                    <p>【玄米価格】 367円/kg</p>
                    <ul class="side-by-side-1">
                        <li>15kg = <?= $rice15 ?></li>
                        <li>30kg = <?= $rice30 ?></li>
                    </ul>
                    <p>150kg以上のご注文された方のみ</p>
                    <ul class="side-by-side-2">
                        <li>5kg = <?= $rice5 ?></li>
                        <li>10kg = <?= $rice10 ?></li>
                    </ul>
                    <p class="notes">※景品・贈答品は米袋代含む</p>
                    <p style="color: red;">※精米後、約1割ほど米ぬかになりますので、<br>実量は15kgを購入の場合、13.5kgでのお届けになるので、ご了承願います</p>
                    <p style="color: red;">※お客様からの消費税は頂いておりません</p>
                </div>
            </div>
        </div>
    </main>
    <?php if (isset($_SESSION[ConstApp::SIGNUP_USER_ID])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
</body>
</html>
