<?php declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
session_cache_limiter('nocache');
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



if (isset($_SESSION['data']['total_order_count'])) {
    list($muck, $count5, $count10, $count15, $count30) = $_SESSION['data']['total_order_count'];
}


$_SESSION['image'] = [];
$menu = new MenuInstance();
$menusArray = $menu->pointMenu();
if (empty($menusArray)){
    $menusObject = [];
}else{
    $menuObject = $menusArray[0];
    $menuId = $menuObject->getMenuId();
    $_SESSION['image'][$menuId]['type'] = $menuObject->getMimeType();
    $_SESSION['image'][$menuId]['data'] = $menuObject->getImageData();
    $_SESSION['image'][$menuId]['last_modified'] = $menuObject->getUpdated_at();
}



if (isset($_SESSION['data']['checkDay']))
{
    $sessionDay = $_SESSION['data']['checkDay'];
} else {
    $sessionDay = null;
}
$calendar = new Calendar($sessionDay);
$calendar->showCalendarBigLot();


$randomId = bin2hex(random_bytes(32));
$_SESSION['largelot_index_token'] = $randomId;

$_SESSION[ConstApp::SIGNUP_DATA] = [];

$_SESSION['data']['menus'][] = $menuObject;


$rice5 = ConstApp::PRICE_RICE_5;
$rice10 = ConstApp::PRICE_RICE_10;
$rice15 = ConstApp::PRICE_RICE_15;
$rice30 = ConstApp::PRICE_RICE_30;
$PriceRice5 = number_format($rice5). '円';
$PriceRice10 = number_format($rice10). '円';
$PriceRice15 = number_format($rice15). '円';
$PriceRice30 = number_format($rice30). '円';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="./assets/largelot-index.css">
    <link rel="stylesheet" href="./assets/notLoginHeader.css">
    <link rel="stylesheet" href="./assets/loginHeader.css">
    <link rel="stylesheet" href="./assets/footer.css">
    <link rel="stylesheet" href="./assets/same.css">
    <script>
        function CalculationResult(){
            let num5 = parseInt(document.getElementById('num5').value) || 0;
            let num10 = parseInt(document.getElementById('num10').value) || 0;
            let num15 = parseInt(document.getElementById('num15').value) || 0;
            let num30 = parseInt(document.getElementById('num30').value) || 0;
            let totalPrice = num5 * <?= $rice5 ?> + num10 * <?= $rice10 ?> + num15 * <?= $rice15 ?> + num30 * <?= $rice30 ?>;
            let totalWeight = (num5 * 5 + num10 * 10 + num15 * 15 + num30 * 30 );
            document.getElementById('totalPrice').textContent = totalPrice.toLocaleString() + '円';
            document.getElementById('totalWeight').textContent = totalWeight.toString() + 'kg';
            if (totalWeight < 150){
                document.getElementById('warningWeight').textContent = '内容量を150kg以上にして下さい';
            }else{
                document.getElementById('warningWeight'). textContent = '';
            }
        }
    </script>
    <title>大ロット商品購入ページ</title>
</head>
<body>
    <?php if (isset($_SESSION[ConstApp::SIGNUP_USER_ID])): ?>
        <header><?php require_once(__DIR__. '/../inc/loginHeader.php') ?></header>
    <?php else: ?>
        <header><?php require_once(__DIR__. '/../inc/notLoginHeader.php') ?></header>
    <?php endif ?>
    <main>
        <div class="login-message">
            <p><?= isset($_SESSION[ConstApp::LOGIN_MESSAGE]) ? $_SESSION[ConstApp::LOGIN_MESSAGE] :'' ?></p>
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
                <li>お届け日1週間以内の注文キャンセルは出来ませんので注意して下さい</li>
            </ul>
        </div>
        <?php if (!empty($menuObject)) : ?>
            <div class="input-field">
                <div class="container">
                    <form action="./menu-detail.php" method="GET">
                        <div class="image">
                            <ul>
                                <li>
                                    <button type="submit" name="menu-detail" value="<?= $menuId ?>">
                                        <img src="image-output.php?id=<?= $menuId ?>" alt="no-image">
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </form>
                    <div class="char">
                        <ul>
                            <li>商品名： <?= $menuObject->getName() ?></li>
                            <li class="small-font">※内容量は150kg以上になるようして下さい</li>
                            <li>5kg (<?= $PriceRice5 ?>) → <input form="order-cfm" type="text" name="rice-breakdown-5" value="<?= isset($count5) ? $count5 : "" ?>" id="num5" size="3" maxlength="3" onkeyup=" CalculationResult()">個</li>
                            <li>10kg (<?= $PriceRice10 ?>) → <input form="order-cfm" type="text" name="rice-breakdown-10" value="<?= isset($count10) ? $count10 : "" ?>" id="num10" size="3" maxlength="3" onkeyup=" CalculationResult()">個</li>
                            <li>15kg (<?= $PriceRice15 ?>) → <input form="order-cfm" type="text" name="rice-breakdown-15" value="<?= isset($count15) ? $count15 : "" ?>" id="num15" size="3" maxlength="3" onkeyup=" CalculationResult()">個</li>
                            <li>30kg (<?= $PriceRice30 ?>) → <input form="order-cfm" type="text" name="rice-breakdown-30" value="<?= isset($count30) ? $count30 : "" ?>" id="num30" size="3" maxlength="3" onkeyup=" CalculationResult()">個</li>
                        </ul>
                        <div class="automatic-calculation">
                            <p>自動計算</p>
                            <ul class="result">
                                <li> 内容量：</li>
                                 <li id="totalWeight"></li>
                                <li id="warningWeight"></li>
                            </ul>
                            <ul class="result">
                                <li> 合計金額：</li>
                                <li id="totalPrice"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>
        <div class="message">
                <p style="color: red;"><?= isset($_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::ORDER]) ? $_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::ORDER] : '' ?></p>
        </div>
        <div class="date-time-selection">
            <div class="subheading">
                <h2>お届け日</h2>
            </div>
            <div class="notes">
                <ul style="color: red;">＜お願い＞
                    <li>注文・配達を急ぎたい場合はLINEで<br>(良彦又は、恵子)へご連絡・ご相談下さい</li>
                    <li>17:00以降の配達になります</li>
                    <li>定休日 → <span class="hurry-holiday"></span></li>
                    <li>大量発注による準備時間(約1週間) → <span class="waitingTime"></span></li>
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
                    <button type="submit" name="largelot_index_token" value="<?= $randomId ?>">購入確認画面へ</button>
                    <p style="color: red;"><?= $_SESSION['message']['riceCount'] ?? '' ?></p>
                </div>
                </form>
        </div>
    </main>
    <?php if (isset($_SESSION['user_id'])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
    <?php $_SESSION[ConstApp::LOGIN_MESSAGE] = '' ?>
    <?php $_SESSION[ConstApp::SIGNUP_MESSAGE] = [] ?>
</body>
</html>