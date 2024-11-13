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
require_once(__DIR__. '/../../../../DB/SelectMenuTable.php');
require_once(__DIR__. '/../../../../instance/menu-instance.php');
require_once(__DIR__. '/../../../../class/ImageExtension.php');
require_once(__DIR__. '/../../../../class/Menu.php');
require_once(__DIR__. '/../../../../class/MenuMoney.php');
require_once(__DIR__. '/../../../../class/MenuPoint.php');
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


switch ($_POST['menu-category'])
{
    case ConstApp::OTHERS:
        $menuCategory = $_POST['other'];
        break;
    default:
        $menuCategory = $_POST['menu-category'];
}

$datas = [
    ConstApp::BUSINESS_SET => $_POST['business-set'],
    ConstApp::MENU_NAME => $_POST['name'],
    ConstApp::MENU_COST => $_POST['cost'],
    ConstApp::MENU_WEIGHT => $_POST['weight'],
    ConstApp::MENU_CATEGORY => $menuCategory,
    ConstApp::MENU_OTHER => $_POST['other'],
    ConstApp::MENU_UNIT => $_POST['menu-unit'],
    ConstApp::MENU_COMMENT_TOP => $_POST['comment-top'],
    ConstApp::MENU_COMMENT_BOTTOM => $_POST['comment-bottom'],
    ConstApp::MENU_NOTES => $_POST['notes']
];
$menu = new MenuMoney($datas);
$menu->deleteEmpty();
$menu->check();
$menu->setMessage();
$menu->make();
$menu->makeEscape();
$_SESSION['moneyData'] = $menu->getDatas();
$_SESSION['moneyMessage'] = $menu->getMessage();
switch ($_FILES['image']['error'])
{
    case 4:
        $imageName = ConstApp::DEFAULT_IMAGE_NAME;
        $imagePass = ConstApp::DEFAULT_IMAGE_PASS;
        $imageType = ConstApp::DEFAULT_IMAGE_TYPE;
        $imageSize = ConstApp::DEFAULT_IMAGE_SIZE;
        $_SESSION['moneyMessageImage'] = [];
        $imageData = file_get_contents($imagePass);
        $_SESSION['image']['name'] = $imageName;
        $_SESSION['image']['tmp_name'] = $imagePass;
        $_SESSION['image']['type'] = $imageType;
        $_SESSION['image']['error'] = $_FILES['image']['error'];
        $_SESSION['image']['size'] = $imageSize;
        break;
        default:
        $image = new ImageExtension($_FILES['image']);
        $image->check();
        $image->setMessage();
        $image->compression();
        $_SESSION['moneyMessageImage'] = $image->getMessage();
        $_SESSION['image'] = $_FILES['image'];
        $_SESSION['image']['type'] = $image->getImageMimeType();
        if ($_SESSION['moneyMessageImage'] !== []){
            header('Location: insert-admin-menu-table.php');
            exit();
        }else {
            $imageType = $image->getImageType();
            $imageData = $image->getImageData();
        }
}
if ($_SESSION['moneyMessage'] !== [] || $_SESSION['moneyMessageImage'] !== [])
{
    header('Location: insert-admin-menu-table.php');
    exit();
} else{
    $_SESSION['data'] = $menu->getDatas();
    $databaseMenu = new MenuInstance();
    $menus = $databaseMenu->moneyMenu();
}

array_push($menus, $menu);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit-insert-menu-table.css">
    <title>Document</title>
</head>
<body>
<h1>こちらでお間違いないか確認して下さい</h1>
    <ul>
        <li>画像:<?= isset($imageName) ? $imageName : $image->getImageName() ?></li>
        <li>画像パス:<?= isset($imagePass) ? $imagePass : $image->getImagePass() ?></li>
        <li>商品名:<?= $menu->getName() ?></li>
        <li><?= $menu->getCostFormat() ?></li>
        <li>商品の種類:<?= $menu->getCategory() ?></li>
        <li>内容量:<?= $menu->getWeight(). $menu->getUnit() ?></li>
        <li>商品説明1:<?= !empty($menu->getCommentTop()) ? $menu->getCommentTop() : '無記入' ?></li>
        <li>商品説明2:<?= !empty($menu->getCommentBottom()) ? $menu->getCommentBottom() : '無記入' ?></li>
        <li>注意事項:<?= !empty($menu->getNotes()) ? $menu->getNotes() : '無記入' ?></li>
    </ul>
    <h2>商品一覧プレビュー画面</h2>
    <br>
    <br>
    <div class="wrapper-order">
    <?php $i = 0 ?>
    <?php for ($j = 0; $j < count($menus); $j++): ?>
        <?php
        if (isset($menus[$j]->datas['menu_image_pass']) && isset($menus[$j]->datas['menu_image_data'])):
            $_SESSION['image'][$j]['type'] = $menus[$j]->getMimeType();
            $_SESSION['image'][$j]['data'] = $menus[$j]->getImageData();
        ?>
        <?php else:
            $_SESSION['image'][$j]['type'] = $imageType;
            $_SESSION['image'][$j]['data'] = $imageData;
        endif
        ?>
        <div class="container">
            <div class="frame">
                <ul>
                    <?php if (isset($menus[$j]->datas['menu_image_pass']) && isset($menus[$j]->datas['menu_image_data'])): ?>
                        <li><img src="insert-image.php?id=<?= $j ?>" alt=""></li>
                    <?php else: ?>
                        <li><a class="box-link" href="display-image.php?id=<?= $j ?>" target="blank"><img src="insert-image.php?id=<?= $j ?>" alt=""></a></li>
                    <?php endif ?>
                </ul>
            </div>
            <div class="char">
                <ul>
                <li>商品名：<?= $menus[$j]->getName() ?></li>
                <li><?= $menus[$j]->getCostFormat() ?></li>
                <li>内容量：<?= $menus[$j]->getWeight(). $menus[$j]->getUnit() ?></li>
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
        </div>
        <?php
            $i = $j;
        ?>
        <?php endfor ?>
    </div>
    <br>
    <br>
    <h2>商品詳細プレビュー画面</h2>
    <br>
    <br>
    <div class="wrapper-info">
        <div class="contai">
            <div class="frame">
                <ul>
                    <li><a class="box-link" href="display-image.php?id=<?= $i ?>" target="blank"><img src="insert-image.php?id=<?= $i ?>" alt=""></a></li>
                </ul>
            </div>
            <div class="char">
                <ul>
                <li>商品名：<?= $menu->getName()?></li>
                <li><?= $menu->getCostFormat() ?></li>
                <li>内容量：<?= $menu->getWeight(). $menu->getUnit() ?></li>
                <li>商品説明：<?= $menu->getTotalComment() ?></li>
                <?php if ($menu->getNotes() !== "" && !is_null($menu->getNotes())): ?>
                <li>注意事項：<?= $menu->getNotes() ?></li>
                <?php else: ?>
                <li>注意事項：ありません</li>
                <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <br>
    <br>
    <h3><a href="insert-admin-menu-table.php">戻る</a></h3>
    <h3><a href="cpl-insert-menu-table.php">商品追加確定へ</a></h3>
</body>
<?php
    $_SESSION['image']['type'] = $_SESSION['image'][$i]['type'];
    $_SESSION['image']['data'] = $_SESSION['image'][$i]['data'];
?>

</html>