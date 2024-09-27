<?php declare(strict_types = 1);
session_cache_limiter('nocache');
header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
session_start();
require_once(__DIR__. '/../../../../DB/LoginWay.php');
require_once(__DIR__. '/../../../../DB/UserModel.php');
require_once(__DIR__. '/../../../../class/MenuMoney.php');
require_once(__DIR__. '/../../../../class/MenuPoint.php');
require_once(__DIR__. '/../../../../class/InsertImage.php');
require_once(__DIR__. '/../../../../DB/InsertMenu.php');


$url = empty($_SERVER['HTTPS']) ? 'http://' : 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$IPaddress = $_SERVER['REMOTE_ADDR'];
$howToLogin = new LoginWay($IPaddress, $url);

if(isset($_SESSION[ConstApp::SIGNUP_USER_ID])){
    $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
    $fairSessionId = $userModel->checkUserId();
    if(!$fairSessionId){
        $howToLogin->destroyCookieAndSession();
        header('Location: ./../../../public/error.php');
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
    header('Location: ./../../../public/index.php');
    exit();
}

$menu = new MenuMoney($_SESSION['data']);
$menu->convertSaveMenuDatabase();
$image = new InsertImage($_SESSION['image']['type'], $_SESSION['image']['tmp_name'], $_SESSION['imageData']);
$image->insertLibrary();
$insert = new InsertMenu();
$insert->insertMenuTable($menu->getDatas(), $image->getImagePass());
header('Location: end-insertMenuTable.php');
return;