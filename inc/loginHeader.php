<!-- サーバー用 -->
<link rel="apple-touch-icon" sizes="180x180" href="/library/apple-touch-icon.png"> <!-- iOS専用 -->
<link rel="manifest" href="/site.webmanifest"> <!-- PWA用 -->
<div class="hamburger-menu">
    <input type="checkbox" id="menu-btn-check">
    <label for="menu-btn-check" class="menu-btn"><span></span></label>
    <div class="menu-content">
        <ul class="ddmenu">
            <li><a href="../index.php"><img src="/library/shopping.png" alt="買い物">商品の購入</a></li>
            <li><a href="../mypage.php"><img src="/library/acount.png" alt="アカウント">マイページ</a></li>
            <li><a href="../company.php"><img src="/library/company.png" alt="会社">会社情報</a></li>
            <li class="pulldown"><a class="setting" href="#"><img src="/library/setting.png" alt="設定">設定</a>
            <ul class="setttings-pulldown">
                <li><a href="../person-info-cfm.php"><img src="/library/person-info.png" alt="個人情報">個人情報の確認・変更</a></li>
                <li><a href="../order-history.php"><img src="/library/order-history.png" alt="注文履歴">注文履歴</a></li>
                <?php
                /* */
                    $logoutNum = bin2hex(random_bytes(32));
                    $_SESSION['logout'] = $logoutNum;
                /* */
                ?>
                <form action="" method="post">
                <li><button class="link-style" type="submit" name="logout" value="<?php /* */ echo $logoutNum /* */ ?>"><img src="/library/logout.png" alt="ログアウト">ログアウト</button></li>
                </form>
            </ul>
            </li>
            <li><a href="../inquiry.php"><img src="/library/phone.png" alt="お問い合わせ">お問い合わせ</a></li>
        </ul>
    </div>
</div>





<!-- ローカル用 -->
<!-- 
<div class="hamburger-menu">
    <input type="checkbox" id="menu-btn-check">
    <label for="menu-btn-check" class="menu-btn"><span></span></label>
    <div class="menu-content">
        <ul class="ddmenu">
            <li><a href="../public/index.php"><img src="../public/library/shopping.png" alt="買い物">商品の購入</a></li>
            <li><a href="../public/mypage.php"><img src="../public/library/acount.png" alt="アカウント">マイページ</a></li>
            <li><a href="../public/company.php"><img src="../public/library/company.png" alt="会社">会社情報</a></li>
            <li class="pulldown"><a class="setting" href="#"><img src="../public/library/setting.png" alt="設定">設定</a>
            <ul class="setttings-pulldown">
                <li><a href="../public/person-info-cfm.php"><img src="../public/library/person-info.png" alt="個人情報">個人情報の確認・変更</a></li>
                <li><a href="../public/order-history.php"><img src="../public/library/order-history.png" alt="注文履歴">注文履歴</a></li>
                <?php
                /*
                    $logoutNum = bin2hex(random_bytes(32));
                    $_SESSION['logout'] = $logoutNum;
                */
                ?>
                <form action="" method="post">
                <li><button class="link-style" type="submit" name="logout" value="<?php /* echo $logoutNum */ ?>"><img src="../public/library/logout.png" alt="ログアウト">ログアウト</button></li>
                </form>
            </ul>
            </li>
            <li><a href="../public/inquiry.php"><img src="../public/library/phone.png" alt="お問い合わせ">お問い合わせ</a></li>
        </ul>
    </div>
</div> -->
