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

require_once(__DIR__. '/../class/Order.php');
require_once(__DIR__. '/../class/Calendar.php');
require_once(__DIR__. '/../class/Message.php');
require_once(__DIR__. '/../configs/constApp.php');
require_once(__DIR__. '/../class/DeliveryDate.php');
require_once(__DIR__. '/../class/Message.php');
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
    $_SESSION[ConstApp::LOGIN_MESSAGE] = 'ログインしてから商品購入して下さい';
    header('Location: login.php');
    exit();
}


if (isset($_POST['index_token']) && $_SESSION['index_token'] === $_POST['index_token']) {
    // ↓商品がセットされているか判断している
    for ($i = 0; $i < 100; $i++){
        $sendName = 'count'. strval($i);
        if (isset($_POST[$sendName])){
            $count = intval($_POST[$sendName]);
            $counts[] = $count;
        } else{
            break;
        }
    }
    if(!isset($counts)){
        header("Location: index.php");
        exit();
    }
    $order = new Order();
    $order->setCounts($counts);
    $order->noneMenu();
    $order->countError();
    $_SESSION[ConstApp::SIGNUP_DATA]['order_count'] = $order->getCounts();
    // ↓$menus内から購入しない商品をunset
    $order->setCountMenu('money');
    $order->unsetMenu();
}elseif (isset($_POST['largelot_index_token']) && $_SESSION['largelot_index_token'] === $_POST['largelot_index_token']){
    $totalWeight = (intval($_POST['rice-breakdown-5']) * 5 + intval($_POST['rice-breakdown-10']) * 10 + intval($_POST['rice-breakdown-15']) * 15 + intval($_POST['rice-breakdown-30']) * 30);
    $counts = [
        intval($totalWeight),
        intval($_POST['rice-breakdown-5']),
        intval($_POST['rice-breakdown-10']),
        intval($_POST['rice-breakdown-15']),
        intval($_POST['rice-breakdown-30'])
    ];
    $order = new Order();
    $order->setCounts($counts);
    $order->noneMenu();
    $order->largelotCountError();
    if (empty($order->getMessage())){
        $_SESSION[ConstApp::SIGNUP_DATA]['total_order_count'] = $order->getCounts();
        $order->variousCalcMiss();
        $order->setCountMenu('point');
        $order->unsetMenu();
    }
}else {
    header('Location: index.php');
    exit();
}



$message = new Message();
// 配達日時が指定されているか判断
if (!isset($_POST['checkDay'])){
    $message->notDelivery(ConstApp::CALENDAR);
    $_SESSION[ConstApp::SIGNUP_MESSAGE] = array_merge_recursive($_SESSION[ConstApp::SIGNUP_MESSAGE], $message->getMessage());
} else {
    $calendar = new Calendar($_POST['checkDay']);
    $calendar->showCalendar();
    $calendar->error();
    if (empty($calendar->getMessage())){
        $_SESSION['data']['checkDay'] = $_POST['checkDay'];
    } else {
        $_SESSION[ConstApp::SIGNUP_MESSAGE] = array_merge_recursive($_SESSION[ConstApp::SIGNUP_MESSAGE], $calendar->getMessage());
    }
}

if (!empty($order->getMessage()) || !empty($message->getMessage()) || !empty($calendar->getMessage())){
    $_SESSION[ConstApp::SIGNUP_MESSAGE] = array_merge_recursive($_SESSION[ConstApp::SIGNUP_MESSAGE], $order->getMessage());
    if (isset($_POST['largelot_index_token'])) {
        header('Location: largelot-index.php');
        exit();
    }else {
        header('Location: index.php');
        exit();
    }
}


$randomId = bin2hex(random_bytes(32));
$_SESSION['order_cfm_token'] = $randomId;



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/order-cfm.css">
    <link rel="stylesheet" href="./assets/notLoginHeader.css">
    <link rel="stylesheet" href="./assets/loginHeader.css">
    <link rel="stylesheet" href="./assets/footer.css">
    <link rel="stylesheet" href="./assets/same.css">
    <title>購入詳細画面</title>
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
            <h2>購入前商品詳細</h2>
        </div>
        <div class="container">
            <?php if (isset($_POST['index_token'])): ?>
            <?php foreach ($order->getMenus() as $menu):?>
                <div class="wrapper-order">
                    <ul>
                    <li>商品名：<?= $menu->getName() ?></li>
                    <li>内容量：<?= $menu->getWeight(). $menu->getUnit(). ' × '. $menu->getCount() ?></li>
                    <?php $menu->subTotalCost() ?>
                    <li><?= $menu->getSubTotalCostFormat() ?></li>
                    </ul>
                </div>
                <?php $allMenu[] = $menu->getDatas() ?>
            <?php endforeach ?>
            <?php $_SESSION[ConstApp::SIGNUP_DATA]['menus'] = $allMenu ?>
            <?php elseif (isset($_POST['largelot_index_token'])): ?>
                <div class="wrapper-order">
                    <ul>
                        <li>商品名：<?= $order->getMenus()[0]->getName() ?></li>
                        <li>内容量：<?= $order->getMenus()[0]->getCount(). $order->getMenus()[0]->getUnit() ?></li>
                        <li>内訳：</li>
                        <?php $menus = $order->getMenus() ?>
                        <?php unset($menus[0]) ?>
                        <?php foreach ($menus as $menu):?>
                        <?php $menu->subTotalCost() ?>
                        <li><?= $menu->getWeight(). $menu->getUnit(). ' × '. $menu->getCount(). '個' ?></li>
                        <?php $allMenu[] = $menu->getDatas() ?>
                        <?php endforeach ?>
                    </ul>
                </div>
                <?php $_SESSION[ConstApp::SIGNUP_DATA]['menus'] = $allMenu ?>
            <?php endif ?>
        </div>
        <h4>支払い方法: 現金</h4>
        <h4>キャンセル日時: お届け日前日まで</h4>
        <h4>お届け日: <?= $calendar->getCheckDay() ?></h4>
        <h2><?= $menu->getTotalCostFormat() ?></h2>
        <div class="edit-forms">
            <?php if(isset($_POST['index_token'])): ?>
            <form action="index.php">
                <button class="left" type="submit">戻る</button>
            </form>
            <?php elseif(isset($_POST['largelot_index_token'])): ?>
            <form action="largelot-index.php">
                <button class="left" type="submit">戻る</button>
            </form>
            <?php endif ?>
            <form action="./order-cpl.php" method="POST">
                <button class="right" type="submit" name="order_cfm_token" value="<?= $randomId ?>">購入完了</button>
            </form>
        </div>
        <?php unset($_POST['index_token']); ?>
        <?php unset($_POST['largelot_index_token']); ?>
        </main>
    <?php if (isset($_SESSION['user_id'])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
</body>
</html>