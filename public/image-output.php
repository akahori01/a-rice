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

if(isset($_SESSION['user_id'])){
    $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
    $fairSessionId = $userModel->checkUserId();
    if(!$fairSessionId){
        $howToLogin->destroyCookieAndSession();
        header('Location: error.php');
        exit();
    }
}

if (isset($_GET['id']) && preg_match('/\A[0-9]+\z/u', $_GET['id']) === 1 && isset($_SESSION['image'][$_GET['id']])) {
    $image = $_SESSION['image'][$_GET['id']];

    // 画像のMIMEタイプを設定
    switch ($image['type'])
    {
        case 'image/jpg':
        case 'image/jpeg':
            header('Content-type: image/jpeg');
            break;
        case 'image/png':
            header('Content-type: image/png');
            break;
        case 'image/gif':
            header('Content-type: image/gif');
            break;
        default:
            header('Location: error.php');
            exit();
    }

    // 画像データを出力
    echo $image['data'];
} else {
    // 画像が存在しない場合の処理
    echo '';
}

// switch ($_SESSION['image']['type'])
// {
//     case 'image/jpeg':
//         header('Content-type: image/jpeg');
//         break;
//     case 'image/jpg':
//         header('Content-type: image/jpg');
//         break;
//     case 'image/png':
//         header('Content-type: image/png');
//         break;
//     case 'image/gif':
//         header('Content-type: image/gif');
//         break;
// }
// print_r($_SESSION['image']['data']);