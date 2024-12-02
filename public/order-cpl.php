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

require_once(__DIR__. '/../class/Menu.php');
require_once(__DIR__. '/../class/Order.php');
require_once(__DIR__. '/../instance/menu-instance.php');
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
} else{
    header('Location: index.php');
    exit();
}
if (!isset($_POST['order_cfm_token']) || $_SESSION['order_cfm_token'] !== $_POST['order_cfm_token']) {
    header('Location: index.php');
    exit();
}

if (empty($_SESSION['data'])){
    header('Location: index.php');
    exit();
}

$userModel->selectId();
$userId = $userModel->getId();

$deliverydate = new DateTime($_SESSION['data']['checkDay']);
$order = new Order();
foreach ($_SESSION['data']['menus'] as $sessionMenu){
    $order->setDatas($sessionMenu);
}
$orderGroup = $order->selectColumnLargeOrderGroup();
$business_set = $_SESSION['data']['menus'][0]['business_set'];
$order->insertOrderTable($userId, $deliverydate->format('Y-m-d'), $business_set, $orderGroup);
$_SESSION['data'] = [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/order-cpl.css">
    <link rel="stylesheet" href="./assets/notLoginHeader.css">
    <link rel="stylesheet" href="./assets/loginHeader.css">
    <link rel="stylesheet" href="./assets/footer.css">
    <link rel="stylesheet" href="./assets/same.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/library/apple-touch-icon.png"> <!-- iOS専用 -->
    <link rel="manifest" href="/site.webmanifest"> <!-- PWA用 -->
    <title>購入完了画面</title>
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
            <h2>購入完了</h2>
        </div>
        <div class="thanks-message">
            <h1>ご購入<br>ありがとうございます</h1>
            <h2>購入した商品は<a href="./mypage.php">マイページ</a>にて<br>確認出来ます</h2>
        </div>
    </main>
    <?php if (isset($_SESSION['user_id'])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
</body>
</html>