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

$url = empty($_SERVER['HTTPS']) ? 'http://' : 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$IPaddress = $_SERVER['REMOTE_ADDR'];
$howToLogin = new LoginWay($IPaddress, $url);

// if(isset($_SESSION[ConstApp::SIGNUP_USER_ID])){
//     $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
//     $fairSessionId = $userModel->checkUserId();
//     if(!$fairSessionId){
//         $howToLogin->destroyCookieAndSession();
//         header('Location: error.php');
//         exit();
//     }
//     $admin = $userModel->selectUserIv();
//     if (!isset($admin) || $admin === false){
//         $howToLogin->destroyCookieAndSession();
//         header('Location: error.php');
//         exit();
//     }

// }else {
//     $howToLogin->destroyCookieAndSession();
//     header('Location: index.php');
//     exit();
// }

switch ($_SESSION['image']['type'])
{
    case 'image/jpeg':
        header('Content-type: image/jpeg');
        break;
    case 'image/jpg':
        header('Content-type: image/jpg');
        break;
    case 'image/png':
        header('Content-type: image/png');
        break;
    case 'image/gif':
        header('Content-type: image/gif');
        break;
}
echo $_SESSION['imageData'];