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
require_once(__DIR__. '/../../../../configs/constApp.php');

$url = empty($_SERVER['HTTPS']) ? 'http://' : 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$IPaddress = $_SERVER['REMOTE_ADDR'];
$howToLogin = new LoginWay($IPaddress, $url);

if(isset($_SESSION[ConstApp::SIGNUP_USER_ID])){
    $userModel = new SelectUserModel($_SESSION[ConstApp::SIGNUP_USER_ID]);
    $fairSessionId = $userModel->checkUserId();
    if(!$fairSessionId){
        $howToLogin->destroyCookieAndSession();
        header('Location: ./../../../error.php');
        exit();
    }
    $admin = $userModel->selectUserIv();
    if (!isset($admin) || $admin === false){
        $howToLogin->destroyCookieAndSession();
        header('Location: ./../../../error.php');
        exit();
    }

}else {
    $howToLogin->destroyCookieAndSession();
    header('Location: ./../../../index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="insert-admin-menu-table.css">
    <title>Document</title>
</head>
<body>
    <h1>商品追加用フォーマット</h1>
    <h3 style="color: red;">[※必須] 始めに選択して下さい 取引設定</h3>
    <div class="tab_container">
            <input id="tab1" type="radio" name="check" checked>
            <label class="tab_item" for="tab1">お金で取引</label>
        <div class="tab_content" id="tab1_content">
            <div class="tab_content_description">
                <form action="edit-insert-menu-table.php" method="POST" enctype="multipart/form-data">
                    <ul>
                        <li>写真: [※拡張子( jpg, jpeg )、※ファイルサイズ( 4000000B = 4000KB = 4MB 未満 )] 選択しない場合デフォルトの画像が表示されます</li>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::MENU_MONEY_MESSAGE_IMAGE][ConstApp::MENU_IMAGE]) ? $_SESSION[ConstApp::MENU_MONEY_MESSAGE_IMAGE][ConstApp::MENU_IMAGE] : '' ?></p>
                        <input type="file" name="image" accept="image/*">
                        <p>↓デフォルト画像</p>
                        <p><img src="<?= ConstApp::DEFAULT_IMAGE_PASS ?>" alt=""></p>
                        <li>[※必須] 商品名(※〇〇kgを入れること):</li>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_NAME]) ? $_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_NAME] : '' ?></p>
                        <input type="text" name="name" placeholder="コシヒカリ(15kg)" value="<?= isset($_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_NAME]) ? $_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_NAME] : '' ?>" required>
                        <li>[※必須] 金額:</li>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_COST]) ? $_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_COST] : '' ?></p>
                        <input type="number" name="cost" placeholder="4800" value="<?= isset($_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_COST]) ? $_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_COST] : '' ?>" required>円
                        <li>[※必須] 内容量(お米の場合、玄米の重量を記入)</li>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_WEIGHT]) ? $_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_WEIGHT] : '' ?></p>
                        <input type="number" name="weight" placeholder="15.00 or 1" value="<?= isset($_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_WEIGHT]) ? $_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_WEIGHT] : '' ?>" required>
                        <li>[※必須] 商品の種類</li>
                        <select name="menu-category">
                            <option value="<?= ConstApp::RICE ?>" <?php if(isset($_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_CATEGORY]) && $_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_CATEGORY] === ConstApp::RICE) {echo "selected";} ?> selected><?= ConstApp::RICE ?></option>
                            <option value="<?= ConstApp::VEGETABLE ?>" <?php if(isset($_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_CATEGORY]) && $_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_CATEGORY] === ConstApp::VEGETABLE) {echo "selected";} ?>><?= ConstApp::VEGETABLE ?></option>
                            <option value="<?= ConstApp::SWEET ?>" <?php if(isset($_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_CATEGORY]) && $_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_CATEGORY] === ConstApp::SWEET) {echo "selected";} ?>><?= ConstApp::SWEET ?></option>
                            <option value="<?= ConstApp::OTHERS ?>" <?php if(isset($_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_CATEGORY]) && $_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_CATEGORY] === $_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_OTHER]) {echo "selected";} ?>><?= ConstApp::OTHERS ?></option>
                        </select>
                        <p>商品の種類が「その他」の場合、詳細を記入</p>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_CATEGORY]) ? $_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_CATEGORY] : '' ?></p>
                        <input type="text" name="other" value="<?= isset($_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_OTHER]) ? $_SESSION[ConstApp::MENU_MONEY_DATA][ConstApp::MENU_OTHER] : '' ?>">
                        <li>[※必須] 商品の単位</li>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_UNIT]) ? $_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_UNIT] : '' ?></p>
                        <select name="menu-unit">
                            <option value="<?= ConstApp::MENU_KG ?>"><?= ConstApp::MENU_KG ?></option>
                            <option value="<?= ConstApp::MENU_G ?>"><?= ConstApp::MENU_G ?></option>
                            <option value="<?= ConstApp::MENU_NUM ?>"><?= ConstApp::MENU_NUM ?></option>
                            <option value="<?= ConstApp::MENU_STALK ?>"><?= ConstApp::MENU_STALK ?></option>
                            <option value="<?= ConstApp::MENU_BUNCH ?>"><?= ConstApp::MENU_BUNCH ?></option>
                            <option value="<?= ConstApp::MENU_HEAD ?>"><?= ConstApp::MENU_HEAD ?></option>
                        </select>
                        <div class="textarea">
                            <div class="text">
                                <li>[※必須] 商品説明1(先頭10文字でわかるように書く様に):</li>
                                <p style="color: red;"><?= isset($_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_COMMENT_TOP]) ? $_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_COMMENT_TOP] : '' ?></p>
                                <textarea name="comment-top" cols="50" rows="20" maxlength="255" placeholder="(例) 静岡県西部で採れた美味しいコシヒカリ。お醤油ちゃんをちょろりと。ん〜〜しょっぱくておいし。" required><?= isset($_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_COMMENT_TOP]) ? $_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_COMMENT_TOP] : '' ?></textarea>
                            </div>
                            <div class="text">
                                <div class="blank-left">
                                    <li>商品説明2(必要ならば):</li>
                                    <p style="color: red;"><?= isset($_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_COMMENT_BOTTOM]) ? $_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_COMMENT_BOTTOM] : '' ?></p>
                                    <textarea name="comment-bottom" cols="50" rows="20" maxlength="255" placeholder="(例) 醤油と相性抜群のコシヒカリ。お醤油ちゃんをちょろりと。ん〜〜しょっぱくておいし。"><?= isset($_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_COMMENT_BOTTOM]) ? $_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_COMMENT_BOTTOM] : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                        <li>※注意事項(先頭10文字に注意事項を書く様に):</li>
                        <p style="color: red;"><?= isset($_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_NOTES]) ? $_SESSION[ConstApp::MENU_MONEY_MESSAGE][ConstApp::MENU_NOTES] : '' ?></p>
                        <textarea name="notes"cols="50" rows="20" maxlength="255" placeholder="(例) 精米後、13.5kgとなります。実量は15.00kgですが、約一割米糠になりますのでのでご了承願います"><?= isset($_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_NOTES]) ? $_SESSION[ConstApp::MENU_MONEY_DATA][Constapp::MENU_NOTES] : '' ?></textarea>
                    </ul>
                    <button type="submit" name="business-set" value="money">プレビュー画面へ</button>
                </form>
                </div>
            </div>
        <a href="start-insertMenuTable.php">戻る</a>
    </div>
</body>
</html>