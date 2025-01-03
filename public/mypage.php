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
require_once(__DIR__. '/../configs/constApp.php');
require_once(__DIR__. '/../configs/constDB.php');
require_once(__DIR__. '/../DB/OrderModel.php');
require_once (__DIR__. '/../class/MenuMoney.php');
require_once (__DIR__. '/../class/MenuPoint.php');



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

$_SESSION['personData'] = [];
$_SESSION[ConstApp::SIGNUP_DATA] = [];
$_SESSION[ConstApp::SIGNUP_MESSAGE] = [];
$_SESSION['image'] = [];


try {
    $admin = $userModel->selectUserIv();
    $orderModel = new OrderModel($userModel->selectId()->getId());
    $orderModel->selectMenu($admin);
} catch(PDOException $e){
    file_put_contents(__DIR__. '/../errorLog/DBError.php', $howToLogin->getCurrentDateTime(). ', 異常名 '. $e->getLine(). $e->getMessage(). "\n", FILE_APPEND | LOCK_EX);
    header('Location: error.php');
    exit();
}




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

$orderModel->separateTheArray();

$userName = $userModel->selectName()->getName();


$oneWeekLaterNowTimeStamp = $howToLogin->getOneWeekLaterDateTimeStamp();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/mypage.css">
    <link rel="stylesheet" href="./assets/notLoginHeader.css">
    <link rel="stylesheet" href="./assets/loginHeader.css">
    <link rel="stylesheet" href="./assets/footer.css">
    <link rel="stylesheet" href="./assets/same.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/library/apple-touch-icon.png"> <!-- iOS専用 -->
    <link rel="manifest" href="/site.webmanifest"> <!-- PWA用 -->
    <title>プロフィール画面</title>
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
            <h2><?= $userName ?>さんのページ</h2>
        </div>
        <?php if (isset($admin) && $admin === true): ?>
            <div class="admin-page">
                <a href="admin/index.php">商品の追加・削除 、 又は休日の追加・削除はこちら</a>
            </div>
        <?php else: ?>
        <div class="notes">
            <ul>
                <li>※支払い方法、現金のみ</li>
                <li>※通常の購入キャンセルは、お届け日の2日前まで</li>
                <li>※150kg以上の購入キャンセルは、お届け日の1週間前まで</li>
                <li>※急遽キャンセルしたい場合は、<a href="inquiry.php">ご連絡ください</a></li>
            </ul>
        </div>
        <?php endif ?>
        <div class="purchase-history">
            <p style="color: red;"><?= isset($_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::MYPAGE]) && !empty($_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::MYPAGE]) ? $_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::MYPAGE] : '' ?></p>
            <tables>
                <h4>購入予約商品</h4>
                <?php if (!empty($orderModel->getNoGroupBeforeDay())): ?>
                    <?php foreach ($orderModel->getNoGroupBeforeDay() as $orderData): ?>
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
                                    <td class="noneBorder"><button name="reset" value="<?= $orderData->getOrderId(). ','. $orderData->getLargeOrderGroup(). ','. $orderData->getDeliverydate(). ',before' ?>" onclick="return confirm('削除してもよろしいですか?')">購入キャンセル</button></td>
                                </form>
                            </tr>
                            </table>
                            <?php else: ?>
                            <?php $menuId = $orderData->getMenuId() ?>
                            <?php $_SESSION['image'][$menuId]['type'] = $orderData->getMimeType() ?>
                            <?php $_SESSION['image'][$menuId]['data'] = $orderData->getImageData() ?>
                            <?php $_SESSION['image'][$menuId]['last_modified'] = $orderData->getUpdated_at() ?>
                            <div class="item-container">
                                <div class="item-img">
                                    <form action="./menu-detail.php" method="GET">
                                        <ul>
                                            <li>
                                                <button type="submit" name="menu-detail" value="<?= $menuId ?>">
                                                    <img src="image-output.php?id=<?= $menuId ?>" alt="no-image">
                                                </button>
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                                <div class="item-text">
                                    <ul>
                                        <li><?= $orderData->getName(). ' ✕ '.$orderData->getCount() ?></li>
                                        <li>お届け日：<?= $orderData->getDeliverydate() ?></li>
                                        <li>金額：<?= $orderModel->noneSubtotal($orderData->getSubTotalCostFormat()) ?></li>
                                        <?php if (strtotime($orderData->getDeliverydate(). '-1 day') > strtotime($howToLogin->getCurrentDate())) : ?>
                                        <form action="" method="post">
                                            <li class="lower-right"><button name="reset" value="<?= $orderData->getOrderId(). ','. $orderData->getLargeOrderGroup(). ','. $orderData->getDeliverydate(). ',before' ?>" onclick="return confirm('削除してもよろしいですか?')">購入キャンセル</button></li>
                                        </form>
                                        <?php endif ?>
                                    </ul>
                                </div>
                            </div>
                            <?php $_SESSION['data']['menus'][] = $orderData ?>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endif ?>
                <?php if (!empty($orderModel->getIsGroupBeforeDay())): ?>
                    <?php foreach($orderModel->getIsGroupBeforeDay() as $orderData): ?>
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
                                            <td class="noneBorder"><button name="reset" value="<?= $orderData[$i]->getOrderId(). ','. $orderData[$i]->getLargeOrderGroup(). ','. $orderData[$i]->getDeliverydate(). ',before' ?>" onclick="return confirm('150kg以上の商品をまとめて削除してもよろしいですか?')">購入キャンセル</button></td>
                                        </form>
                                    <?php endif ?>
                                </tr>
                                </table>
                    <?php if (($i + 1) == count($orderData)): ?>
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
                                            <?php
                                                $objectDateTime = new DateTime($orderData[$i]->getDeliverydate());
                                                $deliveryTimeStamp = $objectDateTime->format('U');
                                                if (($i + 1) == count($orderData) && $oneWeekLaterNowTimeStamp < $deliveryTimeStamp):
                                            ?>
                                                <form action="" method="post">
                                                    <li class="lower-right"><button name="reset" value="<?= $orderData[$i]->getOrderId(). ','. $orderData[$i]->getLargeOrderGroup(). ','. $orderData[$i]->getDeliverydate(). ',before' ?>" onclick="return confirm('150kg以上の商品をまとめて削除してもよろしいですか?')">購入キャンセル</button></li>
                                                </form>
                                            <?php endif ?>
                                        </ul>
                                    </div>
                                </div>
                                <?php $_SESSION['data']['menus'][] = $orderData[$i] ?>
                    <?php if (($i + 1) == count($orderData)): ?>
                        </div>
                    <?php endif ?>
                            <?php endif ?>
                        <?php endfor ?>
                    <?php endforeach ?>
                <?php endif ?>
                <?php if (empty($orderModel->getBeforeDay())): ?>
                <p>無し</p>
                <?php endif ?>

                <h4>本日発送商品</h4>
                <?php if (!empty($orderModel->getNoGroupToday())): ?>
                    <?php foreach ($orderModel->getNoGroupToday() as $orderData): ?>
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
                                    <td class="noneBorder"><button name="reset" value="<?= $orderData->getOrderId(). ','. $orderData->getLargeOrderGroup(). ','. $orderData->getDeliverydate(). ',today' ?>" onclick="return confirm('削除してもよろしいですか?')">購入キャンセル</button></td>
                                </form>
                            </tr>
                            </table>
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
                            <?php $_SESSION['data']['menus'][] = $orderData ?>
                            <?php endif ?>
                    <?php endforeach ?>
                <?php endif ?>
                <?php if (!empty($orderModel->getIsGroupToday())): ?>
                    <?php foreach($orderModel->getIsGroupToday() as $orderData): ?>
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
                                        <td class="noneBorder"><button name="reset" value="<?= $orderData[$i]->getOrderId(). ','. $orderData[$i]->getLargeOrderGroup(). ','. $orderData[$i]->getDeliverydate(). ',today' ?>" onclick="return confirm('150kg以上の商品をまとめて削除してもよろしいですか?')">購入キャンセル</button></td>
                                    </form>
                                    <?php endif ?>
                                </tr>
                                </table>
                    <?php if (($i + 1) == count($orderData)): ?>
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
                    <?php if (($i + 1) == count($orderData)): ?>
                        </div>
                    <?php endif ?>
                            <?php endif ?>
                        <?php endfor ?>
                    <?php endforeach ?>
                <?php endif ?>
                <?php if (empty($orderModel->getToday())): ?>
                <p>無し</p>
                <?php endif ?>
                <?php $counter = 0 ?>

                <h4>購入済商品</h4>
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
                                    <td class="noneBorder"><button name="reset" value="<?= $orderData->getOrderId(). ','. $orderData->getLargeOrderGroup(). ','. $orderData->getDeliverydate(). ',after' ?>" onclick="return confirm('削除してもよろしいですか?')">購入キャンセル</button></td>
                                </form>
                            </tr>
                            </table>
                            <?php $counter++ ?>
                            <?php if($counter >= 3): ?>
                                <?php break ?>
                            <?php endif ?>
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
                        <?php $_SESSION['data']['menus'][] = $orderData ?>
                        <?php $counter++ ?>
                        <?php if($counter >= 3): ?>
                            <?php break ?>
                        <?php endif ?>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php if($counter >= 3): ?>
                        </div>
                    <?php endif ?>
                    <?php if($counter >= 3): ?>
                        <a class="order-list" href="./order-history.php">もっと見る</a>
                    <?php endif ?>
                <?php endif ?>
                <?php if ($counter < 3): ?>
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
                                            <td class="noneBorder"><button name="reset" value="<?= $orderData[$i]->getOrderId(). ','. $orderData[$i]->getLargeOrderGroup(). ','. $orderData[$i]->getDeliverydate(). ',after' ?>" onclick="return confirm('150kg以上の商品をまとめて削除してもよろしいですか?')">購入キャンセル</button></td>
                                        </form>
                                    <?php endif ?>
                                </tr>
                                </table>
                                <?php $counter++ ?>
                                <?php if($counter >= 3): ?>
                                    </div>
                                    <?php break ?>
                                <?php elseif(($i + 1) == count($orderData)) : ?>
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
                                <?php if($counter >= 3): ?>
                                    </div>
                                    <?php break ?>
                                <?php elseif(($i + 1) == count($orderData)) : ?>
                                    </div>
                                <?php endif ?>
                            <?php endif ?>
                        <?php endfor ?>
                        <?php if($counter >= 3): ?>
                            <a class="order-list" href="./order-history.php">もっと見る</a>
                            <?php break ?>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endif ?>
                <?php endif ?>
                <?php if (empty($orderModel->getAfterDay())): ?>
                <p>無し</p>
                <?php endif ?>

            </tables>
        </div>
        <div class="person-info">
            <p>個人情報を変更する場合は<a href="./person-info-cfm.php">こちら</a></p>
        </div>
        <div class="inquiry">
            <ul>
                <li>お困りの際は下記番号へご連絡下さい</li>
                <li>090-5864-0837</li>
            </ul>
        </div>
        <?php $_SESSION[ConstApp::SIGNUP_MESSAGE][ConstApp::MYPAGE] = [] ?>
    </main>
    <?php if (isset($_SESSION[ConstApp::SIGNUP_USER_ID])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
</body>
</html>