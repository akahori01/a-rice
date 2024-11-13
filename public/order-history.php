<?php declare(strict_types=1);
session_cache_limiter('nocache');
session_start();

header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
require_once(__DIR__. '/../DB/LoginWay.php');
require_once(__DIR__. '/../DB/UserModel.php');
require_once(__DIR__. '/../DB/OrderModel.php');
require_once(__DIR__. '/../class/MenuMoney.php');
require_once(__DIR__. '/../class/MenuPoint.php');

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

try {
    $admin = $userModel->selectUserIv();
    $orderModel = new OrderModel($userModel->selectId()->getId());
} catch(PDOException $e){
    file_put_contents(__DIR__. '/../errorLog/DBError.php', $howToLogin->getCurrentDateTime(). ', 異常名 '. $e->getLine(). $e->getMessage(). "\n", FILE_APPEND | LOCK_EX);
    header('Location: error.php');
    exit();
}



$_SESSION['image'] = [];
$randomId = bin2hex(random_bytes(32));


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/order-history.css">
    <link rel="stylesheet" href="./assets/notLoginHeader.css">
    <link rel="stylesheet" href="./assets/loginHeader.css">
    <link rel="stylesheet" href="./assets/footer.css">
    <link rel="stylesheet" href="./assets/same.css">
    <title>購入済み注文履歴</title>
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
        <?php if($admin === true): ?>
            <?php
                if (isset($_POST['order_history_token']) && $_SESSION['order_history_token'] === $_POST['order_history_token']) {
                    $oneYearAgoYear = $_POST['from-year'];
                    $fromMonth = $_POST['from-month'];
                    $fromDay = $_POST['from-day'];
                    $untilYear = $_POST['until-year'];
                    $untilMonth = $_POST['until-month'];
                    $untilDay = $_POST['until-day'];
                    $y = $howToLogin->getCurrentYear();
                    $currentYear = $y;
                }else {
                    $y = $howToLogin->getCurrentYear();
                    $currentYear = $y;
                    $untilYear = $y;
                    $oneYearAgoYear = $howToLogin->getOneYearAgoYear();
                    $fromMonth = $untilMonth = $howToLogin->getCurrentMonth();
                    $fromDay = $untilDay = $howToLogin->getCurrentDay();
                }
            ?>
            <h2>購入済み履歴</h2>
            <form action="" method="POST">
                <p>開始</p>
                <select name="from-year">
                    <?php for($i = -5; $i <= 1; $i++): ?>
                    <?php $y = $y + $i ?>
                    <option value="<?= $y ?>" <?= ($oneYearAgoYear == $y) ? "selected" : "" ?>><?= $y ?></option>
                    <?php $y = $currentYear ?>
                    <?php endfor ?>
                </select>
                <p>年</p>
                <select name="from-month">
                    <?php for($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= ($fromMonth == $i) ? "selected" : "" ?>><?= $i ?></option>
                    <?php endfor ?>
                </select>
                <p>月</p>
                <select name="from-day">
                    <?php for($i = 1; $i <= 31; $i++): ?>
                    <option value="<?= $i ?>" <?= ($fromDay == $i) ? "selected" : "" ?>><?= $i ?></option>
                    <?php endfor ?>
                </select>
                <p>日</p>
                <p class="block">〜</p>
                <p>終了</p>
                <select name="until-year">
                    <?php for($i = -5; $i <= 1; $i++): ?>
                    <?php $y = $y + $i ?>
                    <option value="<?= $y ?>" <?= ($untilYear == $y) ? "selected" : "" ?>><?= $y ?></option>
                    <?php $y = $currentYear ?>
                    <?php endfor ?>
                </select>
                <p>年</p>
                <select name="until-month">
                    <?php for($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= ($untilMonth == $i) ? "selected" : "" ?>><?= $i ?></option>
                    <?php endfor ?>
                </select>
                <p>月</p>
                <select name="until-day">
                    <?php for($i = 1; $i <= 31; $i++): ?>
                    <option value="<?= $i ?>" <?= ($untilDay == $i) ? "selected" : "" ?>><?= $i ?></option>
                    <?php endfor ?>
                </select>
                <p>日</p>
                <div class="serch">
                    <button type="submit" name="order_history_token" value="<?= $randomId ?>">検索</button>
                </div>
            </form>
            <?php
                if (isset($_POST['order_history_token']) && $_SESSION['order_history_token'] === $_POST['order_history_token']) {
                    $startTime = $oneYearAgoYear. '-'. $fromMonth. '-'. $fromDay;
                    $endTime = $untilYear. '-'. $untilMonth. '-'. $untilDay;
                }else {
                    $startTime = $howToLogin->getOneYearAgo();
                    $endTime = $howToLogin->getCurrentDate();
                }
                try{
                    $orderModel->selectMenu($admin, $startTime, $endTime);
                }catch(PDOException $e){
                    file_put_contents(__DIR__. '/../errorLog/DBError.php', $howToLogin->getCurrentDateTime(). ', 異常名 '. $e->getLine(). $e->getMessage(). "\n", FILE_APPEND | LOCK_EX);
                    header('Location: error.php');
                    exit();
                }
            ?>
        <?php else: ?>
            <?php
                try{
                    $orderModel->selectMenu($admin);
                }catch(PDOException $e){
                    file_put_contents(__DIR__. '/../errorLog/DBError.php', $howToLogin->getCurrentDateTime(). ', 異常名 '. $e->getLine(). $e->getMessage(). "\n", FILE_APPEND | LOCK_EX);
                    header('Location: error.php');
                    exit();
                }
            ?>
            <h2>購入済み履歴(過去1年以内)</h2>
        <?php endif ?>
        </div>
        <?php
            if (isset($_POST['reset']) && $_POST['reset'] !== $_SESSION['reset']){
                $_SESSION['reset'] = $_POST['reset'];
                list($resetOrderId, $resetOrderLargeGroupNum, $deliverydate, $resetWeek) = explode(',', $_POST['reset']);
                $orderModel->isProcessDelete(intval($resetOrderId), $resetOrderLargeGroupNum, $deliverydate, $resetWeek, $admin);
                $_SESSION[ConstApp::SIGNUP_MESSAGE] = $orderModel->getMessage();
            } elseif (isset($_POST['reset'])){
                $_SESSION['reset'] = $_POST['reset'];
            }else {
                $_SESSION['reset'] = '';
            }
            $orderModel->separateTheArray()
        ?>
        <p style="color: red;"><?= !empty($_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::MYPAGE]) ? $_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::MYPAGE] : '' ?></p>
        <div class="buy-wrapper">
        <?php $counter = 0 ?>
        <?php if (!empty($orderModel->getNoGroupAfterDay())): ?>
                    <?php foreach ($orderModel->getNoGroupAfterDay() as $orderData): ?>
                        <?php if (isset($admin) && $admin === true): ?>
                            <table>
                            <tr>
                                <th>名前</th>
                                <td style="color: green;"><?= $orderData->getUserName() ?></td>
                            </tr>
                            <tr>
                                <th>商品名</th>
                                <td style="color: green;"><?= $orderData->getName(). ' ✕ '.$orderData->getCount() ?></td>
                            </tr>
                            <tr>
                                <th>小計</th>
                                <td style="color: green;"><?= $orderModel->noneSubtotal($orderData->getSubTotalCostFormat()) ?></td>
                            </tr>
                            <tr>
                                <th>住所</th>
                                <td style="color: green;"><?= $orderData->getAddress() ?></td>
                            </tr>
                                <th>電話番号</th>
                                <td style="color: green;"><?= $orderData->getTel() ?></td>
                            </tr>
                            <tr>
                                <th>日付</th>
                                <td style="color: red;"><?= $orderData->getDeliverydate() ?></td>
                            </tr>
                            <tr>
                                <th></th>
                                <form action="" method="post">
                                    <td class="noneBorder"><button name="reset" value="<?= $orderData->getOrderId(). ','. $orderData->getLargeOrderGroup(). ','. $orderData->getDeliverydate(). ',after' ?>" onclick="return confirm('削除してもよろしいですか?')">消去</button></td>
                                </form>
                            </tr>
                            </table>
                            <?php $counter++ ?>
                        <?php else: ?>
                            <?php $menuId = $orderData->getMenuId() ?>
                            <?php $_SESSION['image'][$menuId]['type'] = $orderData->getMimeType() ?>
                            <?php $_SESSION['image'][$menuId]['data'] = $orderData->getImageData() ?>
                            <?php $_SESSION['image'][$menuId]['last_modified'] = $orderData->getUpdated_at() ?>
                            <div class="item-container">
                                <div class="item-img">
                                    <form action="./menu-detail.php" method="GET">
                                        <ul>
                                            <li><button type="submit" name="menu-detail" value="<?= $menuId ?>"><img src="image-output.php?id=<?= $menuId ?>" alt="no-image"></button></li>
                                        </ul>
                                    </form>
                                </div>
                                <div class="item-text">
                                    <ul>
                                        <li><?= $orderData->getName(). ' ✕ '.$orderData->getCount() ?></li>
                                        <li>お届け日：<?= $orderData->getDeliverydate() ?></li>
                                        <li>金額：<?= $orderModel->noneSubtotal($orderData->getSubTotalCostFormat()) ?></li>
                                    </ul>
                                </div>
                            </div>
                            <?php $counter++ ?>
                        <?php $_SESSION['data']['menus'][] = $orderData ?>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php if ($counter % 3 !== 0) : ?>
                        <?php $orderBlankFill = 3 - ($counter % 3) ?>
                        <?php for ($i = 0; $i < $orderBlankFill; $i++): ?>
                            <div class="order-blank-fill"><p></p></div>
                            <?php endfor ?>
                    <?php endif ?>
                <?php endif ?>
                <?php $counter = 0 ?>
                <?php if (!empty($orderModel->getIsGroupAfterDay())): ?>
                    <?php foreach($orderModel->getIsGroupAfterDay() as $orderData): ?>
                        <div class="big-lot-frame">
                        <?php for($i = 0; $i < count($orderData); $i++): ?>
                            <?php if (isset($admin) && $admin === true): ?>
                                <table>
                                <tr>
                                    <th>名前</th>
                                    <td style="color: green;"><?= $orderData[$i]->getUserName() ?></td>
                                </tr>
                                <tr>
                                    <th>商品名</th>
                                    <td style="color: green;"><?= $orderData[$i]->getName(). ' ✕ '.$orderData[$i]->getCount() ?></td>
                                </tr>
                                <tr>
                                    <th>小計</th>
                                    <td style="color: green;"><?= $orderModel->noneSubtotal($orderData[$i]->getSubTotalCostFormat()) ?></td>
                                </tr>
                                <tr>
                                    <th>住所</th>
                                    <td style="color: green;"><?= $orderData[$i]->getAddress() ?></td>
                                </tr>
                                    <th>電話番号</th>
                                    <td style="color: green;"><?= $orderData[$i]->getTel() ?></td>
                                </tr>
                                <tr>
                                    <th>日付</th>
                                    <td style="color: red;"><?= $orderData[$i]->getDeliverydate() ?></td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <?php if (($i + 1) == count($orderData)): ?>
                                        <form action="" method="post">
                                            <td class="noneBorder"><button name="reset" value="<?= $orderData[$i]->getOrderId(). ','. $orderData[$i]->getLargeOrderGroup(). ','. $orderData[$i]->getDeliverydate(). ',after' ?>" onclick="return confirm('150kg以上の商品をまとめて削除してもよろしいですか?')">消去</button></td>
                                        </form>
                                    <?php endif ?>
                                </tr>
                                </table>
                                <?php $counter++ ?>
                                <?php if (($i + 1) == count($orderData)): ?>
                                    <?php if ($counter % 3 !== 0) : ?>
                                        <?php $orderBlankFill = 3 - ($counter % 3) ?>
                                        <?php for ($j = 0; $j < $orderBlankFill; $j++): ?>
                                            <div class="order-blank-fill"><p></p></div>
                                        <?php endfor ?>
                                    <?php endif ?>
                        </div>
                                <?php endif ?>
                            <?php else: ?>
                                <?php $menuId = $orderData[$i]->getMenuId() ?>
                                <?php $_SESSION['image'][$menuId]['type'] = $orderData[$i]->getMimeType() ?>
                                <?php $_SESSION['image'][$menuId]['data'] = $orderData[$i]->getImageData() ?>
                                <?php $_SESSION['image'][$menuId]['last_modified'] = $orderData[$i]->getUpdated_at() ?>
                                <div class="item-container">
                                    <div class="item-img">
                                        <form action="./menu-detail.php" method="GET">
                                            <ul>
                                                <li><button type="submit" name="menu-detail" value="<?= $menuId ?>"><img src="image-output.php?id=<?= $menuId ?>" alt="no-image"></button></li>
                                            </ul>
                                        </form>
                                    </div>
                                    <div class="item-text">
                                        <ul>
                                            <li><?= $orderData[$i]->getName(). ' ✕ '.$orderData[$i]->getCount() ?></li>
                                            <li>お届け日：<?= $orderData[$i]->getDeliverydate() ?></li>
                                            <li>金額：<?= $orderModel->noneSubtotal($orderData[$i]->getSubTotalCostFormat()) ?></li>
                                        </ul>
                                    </div>
                                </div>
                                <?php $_SESSION['data']['menus'][] = $orderData[$i] ?>
                                <?php $counter++ ?>
                                <?php if (($i + 1) == count($orderData)): ?>
                                    <?php if ($counter % 3 !== 0) : ?>
                                        <?php $orderBlankFill = 3 - ($counter % 3) ?>
                                        <?php for ($j = 0; $j < $orderBlankFill; $j++): ?>
                                            <div class="order-blank-fill"><p></p></div>
                                        <?php endfor ?>
                                    <?php endif ?>
                        </div>
                                <?php endif ?>
                            <?php endif ?>
                        <?php endfor ?>
                    <?php endforeach ?>
                <?php endif ?>
                <?php if (empty($orderModel->getAfterDay())): ?>
                <p>購入済み履歴無し</p>
                <?php endif ?>

        </div>
        <?php $_SESSION['order_history_token'] = $randomId ?>
    </main>
    <?php if (isset($_SESSION[ConstApp::SIGNUP_USER_ID])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
    <?php $_SESSION[ConstApp::SIGNUP_MESSAGE] = [] ?>
</body>
</html>
