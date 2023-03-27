<?php 
    declare(strict_types=1);

    try {
        require_once (dirname(__FILE__, 2) . "/components/header/header.php" );
        require_once (dirname(__FILE__, 3) . "/app/constants/reserveTimeRangeData.php"); // $reserveTimeRangeList読み込み

    } catch(Exception $e) {
        escapePageToErrPage();
    }

    // 内容確認ページからの流入でない場合はアクセス禁止
    if(!isset($_SESSION['delete_success_flag'])) escapePageToHomeMenu();
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
    <p class="lead-text">予約情報をキャンセルしました。</p>
    <p>またのご予約をお待ちしています。</p>

    <div class="wrapper__link-area">
        <a href="./homeMenu.php">トップページへ</a>
        <a href="./reserve-yearMonthCalender-page.php">予約ページへ</a>
    </div>
</div>

<?php
    // ブラウザ表示完了時点でセッション変数の破棄
    unset($_SESSION['deleteTgtInfos']);
?>