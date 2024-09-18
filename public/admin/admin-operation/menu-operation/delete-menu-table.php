<?php declare(strict_types=1);
session_cache_limiter('nocache');
header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
session_start();
require_once(__DIR__. '/../../../DB/LoginWay.php');
require_once(__DIR__. '/../../../DB/UserModel.php');
require_once(__DIR__. '/../../../instance/menu-instance.php');
require_once(__DIR__. '/../../../class/MenuMoney.php');


$url = empty($_SERVER['HTTPS']) ? 'http://' : 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$IPaddress = $_SERVER['REMOTE_ADDR'];
$howToLogin = new LoginWay($IPaddress, $url);

if(isset($_SESSION[ConstApp::SIGNUP_USER_ID])){
    $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
    $fairSessionId = $userModel->checkUserId();
    if(!$fairSessionId){
        $howToLogin->destroyCookieAndSession();
        header('Location: ./../../../app/error.php');
        exit();
    }
    $admin = $userModel->selectUserIv();
    if (!isset($admin) || $admin === false){
        $howToLogin->destroyCookieAndSession();
        header('Location: error.php');
        exit();
    }

}else {
    $howToLogin->destroyCookieAndSession();
    header('Location: ./../../../app/index.php');
    exit();
}

$databaseMenu = new MenuInstance();
if (isset($_POST['delete-id'])){
    $menuclass = new MenuMoney(null);
    $menuclass->deleteMenu($_POST['delete-id']);
}
$menus = $databaseMenu->moneyMenu();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="delete-menu-table.css">
    <title>Document</title>
</head>
<body>
<br>
<br>
<a href="./../../index.php">戻る</a>

<h1 style="color: red;">削除したい商品を誰も(購入予約又は、本日発送)にないことを確認してから削除してください</h1>

    <h2>商品一覧プレビュー画面</h2>
    <br>
    <br>
    <form action="" method="POST">
    <div class="wrapper-order">
        <?php foreach ($menus as $menu): ?>
        <div class="container">
            <div class="frame">
                <ul>
                    <?php if (isset($menu->datas['menu_image_pass'])): ?>
                        <!-- ↓相対パスのみじゃないから表示されないよ -->
                    <li><img src="<?= $menu->datas['menu_image_pass'] ?>" alt=""></li>
                    <?php else: ?>
                    <li><a class="box-link" href="display-image.php" target="blank"><?= isset($imageName) ? $imageName : $image->getImageName() ?></a></li>
                    <?php endif ?>
                </ul>
            </div>
            <div class="char">
                <ul>
                <li>商品名：<?= $menu->getName() ?></li>
                <li><?= $menu->getCostFormat() ?></li>
                <li>内容量：<?= $menu->getWeight(). $menu->getUnit() ?></li>
                    <li>数量：<select name="count">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </li>
                </ul>
            </div>
            <button type="submit" name="delete-id" value="<?= $menu->getMenuId() ?>" onclick="return confirm('削除してもよろしいですか?')">削除する</button>
        </div>
        <?php endforeach ?>
    </div>
</form>
</body>
</html>