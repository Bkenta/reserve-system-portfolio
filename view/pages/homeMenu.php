<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header.php');

    } catch (Exception $e) {
        escapePageToErrPage();
    }
?>
<?php 
    // タイトル・ログアウトボタンの読み込み
    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header-under-content.php');

    } catch(Exception $e) {
        escapePageToErrPage();
    }
?>
<div class="wrapper">
    <div class="to-contentPage-btn-area disp-grid grid-just-center grid-align-center">
        <div class="to-contentPage-btn-area_toReservePage btn-big grid-align-center btn-hover-opacity">
            <a href="./reserve-yearMonthCalender-page.php"></a>
            <img src="../assets/img/mic-icon.png" alt="マイクアイコン">
            <span>予約する</span>
        </div>
        <div class="to-contentPage-btn-area_toReserveConfirmPage btn-big grid-align-center btn-hover-opacity">
            <a href="./reserve-condition-page.php"></a>
            <img src="../assets/img/calender-icon.png" alt="カレンダーアイコン">
            <span>予約確認</span>
        </div>
    </div>
</div>