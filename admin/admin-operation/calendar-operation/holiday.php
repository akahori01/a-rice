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
require_once(__DIR__. '/../../../class/Calendar.php');

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
$calendar = new Calendar(null, null);
if (isset($_POST['checkDay'])){
    $calendar->insertholiday($_POST['checkDay']);
}
if (isset($_POST['deleteHoliday'])){
    $calendar->deleteholiday($_POST['deleteHoliday']);
}
$calendar->showCalendar();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="holiday.css">
</head>
<body>
<a href="./../../index.php">トップページに戻る</a>
<h1 style="color: red;">休日にしたい日に、購入予約がないことを確認して下さい</h1>
<h1 style="color: red;">購入予約がある場合は、電話にて説明して下さい</h1>
    <div class="add-holiday">
    <h2>休日の追加</h2>
    <form action="" method="POST">
        <?php foreach ($calendar->getCalendars() as $date): ?>
            <table class="calendar">
                <caption><h3><?= $date['title'] ?></h3></caption>
                <tr>
                    <th>日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th>土</th>
                </tr>
                <?php foreach ($date['weeks'] as $week): ?>
                    <?= $week ?>
                    <?php endforeach ?>
            </table>
        <?php endforeach ?>
        <p><button>選択した日を定休日にする</button></p>
    </form>
    </div>
    <br>
    <br>
    <br>
    <div class="delete-holiday">
    <h2>休日の削除</h2>
    <form action="" method="POST">
        <select name="deleteHoliday">
        <?php foreach ($calendar->getholidate() as $day): ?>
            <option value="<?= $day ?>"><?= $day ?></option>
            <?php endforeach ?>
        </select>
        <p><button>記入日を営業日にする</button></p>
    </form>
    </div>
</body>
</html>