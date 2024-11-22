<?php declare(strict_types=1);
session_cache_limiter('nocache');
header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
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

    // ETagの作成（画像データのMD5ハッシュ）
    $etag = md5($image['data']);
    $lastModified = strtotime($image['last_modified']);


    // キャッシュ制御ヘッダー
    header("Cache-Control: public, max-age=604800"); // 1週間キャッシュ
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + 604800) . " GMT"); // Expiresヘッダー

    // Last-Modifiedヘッダー
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");

    // ETagヘッダー
    header("ETag: \"$etag\"");

    // キャッシュ判定
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
        // ETagが一致する場合、304 Not Modifiedを返す
        header("HTTP/1.1 304 Not Modified");
        exit;
    }

    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) {
        // 最終更新日時が一致する場合、304 Not Modifiedを返す
        header("HTTP/1.1 304 Not Modified");
        exit;
    }




    // 画像のMIMEタイプを設定
    switch ($image['type'])
    {
        case 'image/jpg':
        case 'image/jpeg':
            header('Content-type: image/jpeg');
            break;
        default:
            header('Location: error.php');
            exit();
    }


    // 画像データを出力
    unset($_SESSION['image'][$_GET['id']]);
    echo $image['data'];
} else {
    // 画像が存在しない場合の処理
    echo '';
}