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
require_once(__DIR__. '/../DB/MenuModel.php');
require_once(__DIR__. '/../instance/menu-instance.php');
require_once(__DIR__. '/../configs/constApp.php');
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
}


if (isset($_GET['menu-detail']) && preg_match('/\A[0-9]+\z/u', $_GET['menu-detail']) === 1){
    $menuId = $_GET['menu-detail'];
}else{
    header('Location: error.php');
    exit();
}

$menuModel = new MenuModel();
$menuDatabaseAll = $menuModel->menuAll()->getAll();
foreach($menuDatabaseAll as $menuDatabase){
    if ($menuDatabase['business_set'] === 0){
        $menuInstance = new MenuMoney($menuDatabase);
        $menuInstance->make();
        $instanceMenuAll[] = $menuInstance;
    }elseif ($menuDatabase['business_set'] === 1){
        $menuInstance = new MenuPoint($menuDatabase);
        $menuInstance->make();
        $instanceMenuAll[] = $menuInstance;
    }
}
foreach ($instanceMenuAll as $key => $menuInstance){
    if (intval($menuId) !== $menuInstance->getMenuId()){
        unset($instanceMenuAll[$key]);
    } else{
        $choiceMenu = $menuInstance;
    }

}
if (!isset($choiceMenu)){
    header('Location: error.php');
}

$_SESSION['image'] = [];
$_SESSION['image'][$menuId]['type'] = $choiceMenu->getMimeType();
$_SESSION['image'][$menuId]['data'] = $choiceMenu->getImageData();
$_SESSION['image'][$menuId]['last_modified'] = $choiceMenu->getUpdated_at();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/menu-detail.css">
    <link rel="stylesheet" href="./assets/notLoginHeader.css">
    <link rel="stylesheet" href="./assets/loginHeader.css">
    <link rel="stylesheet" href="./assets/footer.css">
    <link rel="stylesheet" href="./assets/same.css">
    <title>商品詳細画面</title>
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
            <h2>商品詳細</h2>
        </div>
        <div class="wrapper-info">
            <h2>商品名：<?= $choiceMenu->getName() ?></h2>
            <div class="container">
                <div class="image">
                    <ul>
                        <li><img src="image-output.php?id=<?= $menuId ?>" alt="お米"></li>
                    </ul>
                </div>
                <div class="char">
                    <ul>
                    <?php if($choiceMenu->datas['menu_id'] !== ConstAPP::LARGELOT_ORIGINAL_MANU_ID): ?>
                        <li><?= $choiceMenu->getCostFormat() ?></li>
                        <li>内容量：<?= $choiceMenu->getWeight(). $choiceMenu->getUnit() ?></li>
                    <?php endif ?>
                    <li>商品説明：<?= $choiceMenu->getTotalComment() ?></li>
                    <?php if ($choiceMenu->getNotes() !== "" && !is_null($choiceMenu->getNotes())): ?>
                    <li>注意事項：<?= $choiceMenu->getNotes() ?></li>
                    <?php else: ?>
                    <li>注意事項：ありません</li>
                    <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="transition">
            <?php if($choiceMenu->datas['business_set'] === 0): ?>
                <a href="index.php">戻る</a>
            <?php else: ?>
                <a href="largelot-index.php">戻る</a>
            <?php endif ?>
        </div>
    </main>
    <?php if (isset($_SESSION[ConstApp::SIGNUP_USER_ID])): ?>
        <footer><?php require_once(__DIR__. '/../inc/loginFooter.php') ?></footer>
    <?php else: ?>
        <footer><?php require_once(__DIR__. '/../inc/notLoginFooter.php') ?></footer>
    <?php endif ?>
</body>
</html>