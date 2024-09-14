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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="display-image.css">
    <title>Document</title>
</head>
<body>
    <ul>
    <div class="itemlist-bigpc">
        <h3>商品一覧ページの画像サイズ(BIG PC)用</h3>
        <li><img src="insert-image.php" alt=""></li>
    </div>
    <div class="itemlist-smallpc">
        <h3>商品一覧ページの画像サイズ(small PC)用</h3>
        <li><img src="insert-image.php" alt=""></li>
    </div>
    <div class="itemlist-tablet">
        <h3>商品一覧ページの画像サイズ(タブレット)用</h3>
        <li><img src="insert-image.php" alt=""></li>
    </div>
    <div class="itemlist-mobile">
        <h3>商品一覧ページの画像サイズ(iphone or Android)用</h3>
        <li><img src="insert-image.php" alt=""></li>
    </div>
    <div class="iteminfo-bigpc">
        <h3>商品詳細ページの画像サイズ(BIG PC)用</h3>
        <li><img src="insert-image.php" alt=""></li>
    </div>
    <div class="iteminfo-smallpc">
        <h3>商品詳細ページの画像サイズ(small PC)用</h3>
        <li><img src="insert-image.php" alt=""></li>
    </div>
    <div class="iteminfo-tablet">
        <h3>商品詳細ページの画像サイズ(タブレット)用</h3>
        <li><img src="insert-image.php" alt=""></li>
    </div>
    <div class="iteminfo-mobile">
        <h3>商品詳細ページの画像サイズ(iphone or Android)用</h3>
        <li><img src="insert-image.php" alt=""></li>
    </div>
    </ul>
</body>
</html>