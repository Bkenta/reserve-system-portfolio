<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/escape.php');
        require_once(dirname(__FILE__, 3) . '/app/constants/reserveTimeRangeData.php');       // $reserveTimeRangeList読み込み
        require_once(dirname(__FILE__, 3) . '/app/functions/escapePage/escapeToOtherPage.php');
        
    } catch (Exception $e) {
        escapePageToErrPage();
    }

    // URLによる直接流入はアクセス禁止
    if(!isset($_SESSION['updateTgtInfos']['reservePeopleCnt'])) escapePageToHomeMenu();
?>
<?php 
    // タイトル・ログアウトボタンの読み込み
    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header-under-content.php');

    } catch(Exception $e) {
        escapePageToErrPage();
    }
?>

<p class="lead-text">予約情報を下記の内容に変更しました。</p>
<div class="reserve-update-success-wrapper">
    <div class="reserve-update-success-wrapper__date disp-grid grid-just-center">
        <p>予約日</p>
        <p><?php echo $_SESSION['updateTgtInfos']['reserveYear']  . '年' 
                    . $_SESSION['updateTgtInfos']['reserveMonth'] . '月' 
                    . $_SESSION['updateTgtInfos']['reserveDay']   . '日'; ?>
        </p>
    </div>
    <div class="reserve-update-success-wrapper__order-times disp-grid grid-just-center">
        <p>予約時間</p>
        <div>     
            <?php $reservedTimeRangeIDs = explode(',', $_SESSION['updateTgtInfos']['reserveTimeRanges']); ?>
            <?php foreach($reservedTimeRangeIDs as $utilization_range_id): ?>
                <p><?php echo escapeHtml($reserveTimeRangeList[ $utilization_range_id - 1 ]); ?></p>
            <?php endforeach ?>
        </div>
    </div>
    <div class="reserve-update-success-wrapper__person-num disp-grid grid-just-center">
        <p>利用人数</p>
        <p><?php echo escapeHtml($_SESSION['updateTgtInfos']['reservePeopleCnt']); ?>名様</p>
    </div>
    <div class="reserve-update-success-wrapper__person-name disp-grid grid-just-center">
        <p>氏名</p>
        <p><?php echo escapeHtml($_SESSION['user_info']['first_name']) . escapeHtml($_SESSION['user_info']['last_name']) . " 様"; ?></p>
    </div>
    <div class="reserve-update-success-wrapper__person-tel disp-grid grid-just-center">
        <p>電話番号（連絡先）</p>
        <p><?php echo escapeHtml($_SESSION['updateTgtInfos']['contactPhoneNumber']); ?></p>
    </div>
    <div class="reserve-update-success-wrapper__person-email disp-grid grid-just-center">
        <p>E-mail（連絡先）</p>
        <p><?php echo escapeHtml($_SESSION['updateTgtInfos']['contactMailAddress']); ?></p>
    </div>
    <div class="reserve-update-success-wrapper__btn disp-grid grid-just-center">
        <a href="./homeMenu.php">トップページへ</a>
        <a href="./reserve-condition-page.php">内容確認ページへ</a>
    </div>
</div>

<?php
    // ブラウザ表示完了時点でセッション変数の破棄
    unset($_SESSION['updateTgtInfos']);
?>