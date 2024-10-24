<?php declare(strict_types=1);
session_cache_limiter('nocache');
header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
session_start();

require_once(__DIR__. '/../DB/LoginWay.php');
require_once(__DIR__. '/../DB/UserModel.php');
require_once(__DIR__. '/../class/DeliveryDate.php');
require_once(__DIR__. '/../class/Calendar.php');
require_once(__DIR__. '/../class/Message.php');
require_once(__DIR__. '/../instance/menu-instance.php');
require_once(__DIR__. '/../configs/constApp.php');
require_once(__DIR__. '/../configs/constClass.php');
require_once(__DIR__. '/../configs/constDB.php');


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

if (empty($_SESSION['message']['order'])){
    if (isset($_SESSION['data']['order_count'])) {
        $counts = $_SESSION['data']['order_count'];
    }
}

$menu = new MenuInstance();
$menus = $menu->moneyMenu();
$menus = isset($menus) ? $menus : [];

if (isset($_SESSION['data']['checkDay']))
{
    $sessionDay = $_SESSION['data']['checkDay'];
} else {
    $sessionDay = null;
}
$calendar = new Calendar($sessionDay);
$calendar->showCalendar();


$randomId = bin2hex(random_bytes(32));
$_SESSION['index_token'] = $randomId;

$_SESSION[ConstApp::SIGNUP_DATA] = [];

if (!isset($_SESSION[ConstApp::LOGIN_MESSAGE]) && empty($_SESSION[ConstApp::LOGIN_MESSAGE])){
    $_SESSION[ConstApp::LOGIN_MESSAGE] = 'ログアウトしました';
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="./assets/index.css">
    <link rel="stylesheet" href="./assets/notLoginHeader.css">
    <link rel="stylesheet" href="./assets/loginHeader.css">
    <link rel="stylesheet" href="./assets/footer.css">
    <link rel="stylesheet" href="./assets/same.css">
    <title>商品購入ページ</title>
</head>
<body>
    <?php if (isset($_SESSION[ConstApp::SIGNUP_USER_ID])): ?>
        <header><?php require_once(__DIR__. '/../inc/loginHeader.php') ?></header>
    <?php else: ?>
        <header><?php require_once(__DIR__. '/../inc/notLoginHeader.php') ?></header>
    <?php endif ?>
    <main>
        <?php
            var_dump(session_id()); // セッションIDを確認
            var_dump($_SESSION); // セッションデータを確認
            var_dump($_COOKIE);
        ?>
        <div class="login-message">
            <p><?= isset($_SESSION[ConstApp::LOGIN_MESSAGE]) && !empty($_SESSION[ConstApp::LOGIN_MESSAGE]) ? $_SESSION[ConstApp::LOGIN_MESSAGE] :'' ?></p>
        </div>
        <div class="title">
            <h1>赤堀産地の<br>コシヒカリ</h1>
        </div>
        <div class="subheading">
            <h2>商品購入</h2>
        </div>
        <div class="notes">
                <ul style="color: red;">＜お願い＞
                    <li>現金のみでの支払いになります</li>
                </ul>
            </div>
        <div class="input-field">
            <?php if (!empty($menus)) : ?>
            <?php for ($i = 0; $i < count($menus); $i++): ?>
                <?php $_SESSION['image'][$i]['type'] = $menus[$i]->getMimeType() ?>
                <?php $_SESSION['image'][$i]['data'] = $menus[$i]->getImageData() ?>
                <div class="container">
                <form action="./menu-detail.php" method="GET">
                    <div class="image">
                        <ul>
                            <li>
                                <button type="submit" name="menu-detail" value="<?= $menus[$i]->getMenuId() ?>">
                                    <img src="image-output.php?id=<?= $i ?>" alt="no-image">
                                </button>
                            </li>
                        </ul>
                    </div>
                </form>
                <div class="char">
                    <ul>
                        <li>商品名：<?= $menus[$i]->getName() ?></li>
                        <li><?= $menus[$i]->getCostFormat() ?></li>
                        <li>内容量：<?= $menus[$i]->getWeight(). $menus[$i]->getUnit() ?></li>
                        <li>数量：
                            <select name="<?= 'count'. strval($i) ?>" form="order-cfm">
                                <option value="0" <?php if(isset($counts) && strval($counts[$i]) === '0') {echo "selected";} ?> selected>0</option>
                                <option value="1" <?php if(isset($counts) && strval($counts[$i]) === '1') {echo "selected";} ?>>1</option>
                                <option value="2" <?php if(isset($counts) && strval($counts[$i]) === '2') {echo "selected";} ?>>2</option>
                                <option value="3" <?php if(isset($counts) && strval($counts[$i]) === '3') {echo "selected";} ?>>3</option>
                                <option value="4" <?php if(isset($counts) && strval($counts[$i]) === '4') {echo "selected";} ?>>4</option>
                                <option value="5" <?php if(isset($counts) && strval($counts[$i]) === '5') {echo "selected";} ?>>5</option>
                            </select>
                        </li>
                    </ul>
                </div>
            </div>
            <?php $_SESSION['data']['menus'][] = $menus[$i] ?>
            <?php endfor ?>
            <?php endif ?>
        </div>
        <p><a href="largelot-index.php">150kg以上のお米を一度に注文される方はこちら</a></p>
        <img src="image-output.php?id=0" alt="no-image">
        <div class="message">
                <p style="color: red;"><?= isset($_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::ORDER]) ? $_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::ORDER] : '' ?></p>
        </div>
        <div class="date-time-selection">
            <div class="subheading">
                <h2>お届け日</h2>
            </div>
            <div class="notes">
                <ul style="color: red;">＜お願い＞
                    <li>当日注文・配達の場合はLINEで<br>(良彦又は、恵子)へご連絡下さい</li>
                    <li>17:00以降の配達になります</li>
                    <li>定休日 → <span class="hurry-holiday"></span></li>
                </ul>
            </div>
            <div class="calendar-form">
                <form action="./order-cfm.php" method="POST" id="order-cfm">
                        <?php foreach ($calendar->getCalendars() as $date): ?>
                            <table class="calendar">
                                <caption><h3><?= $date['title'] ?></h3></caption>
                                <tr>
                                    <th>日</th>
                                    <th>月</th>
                                    <th>火</th>
                                    <th>水</th>
                                    <th>木</th>
                                    <th>金</th>
                                    <th>土</th>
                                </tr>
                                <?php foreach ($date['weeks'] as $week): ?>
                                    <?= $week ?>
                                <?php endforeach ?>
                            </table>
                            <span class="indention"></span>
                            <?php endforeach ?>
            </div>
                <div class="message">
                    <p style="color: red;"><?= isset($_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::CALENDAR]) ? $_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::CALENDAR] : '' ?></p>
                </div>
                <div class="serch">
                    <button type="submit" name="index_token" value="<?= $randomId ?>">購入詳細画面へ</button>
                    <p style="color: red;"><?= $_SESSION['message']['riceCount'] ?? '' ?></p>
                </div>
                </form>
        </div>
    <?php $_SESSION[ConstApp::LOGIN_MESSAGE] = '' ?>
    <?php $_SESSION[ConstApp::SIGNUP_MESSAGE] = [] ?>
    </main>
    <?php if (isset($_SESSION['user_id'])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
</body>
</html>
