<?php 
    declare (strict_types=1);

    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/db/dbConnection.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/escape.php');
        require_once(dirname(__FILE__, 3) . '/app/constants/reserveTimeRangeData.php');       // $reserveTimeRangeList読み込み
        require_once(dirname(__FILE__, 3) . '/app/functions/escapePage/escapeToOtherPage.php');    

    } catch(Exception $e) {
        escapePageToErrPage();
    }

    // URLによる直接アクセスを拒否
    if(!(isset($_SESSION['reserve_info_data']['user_num']) && 
         isset($_SESSION['reserve_info_data']['contact_phone_number']) && 
         isset($_SESSION['reserve_info_data']['contact_mailAddress']))) escapePageToHomeMenu();

    // 予約情報の格納
    $selectedYear       = $_SESSION['reserve_info_data']['selected_year'];
    $selectedMonth      = $_SESSION['reserve_info_data']['selected_month'];
    $selectedDay        = $_SESSION['reserve_info_data']['selected_day'];
    $selectedTimeRanges = $_SESSION['reserve_info_data']['selected_time_range'];
    $userNum            = $_SESSION['reserve_info_data']['user_num'];
    $contactPhoneNumber = $_SESSION['reserve_info_data']['contact_phone_number'];
    $contactMailAddress = $_SESSION['reserve_info_data']['contact_mailAddress'];
    $usersTableId       = $_SESSION['user_info']['users_table_id'];
    // 年月日
    $selectedDate       = $selectedYear . '年' . $selectedMonth . '月' . $selectedDay . '日';

    // 「予約」ボタン押下時の処理
    // 選択されている予約枠の数だけレコードを登録する
    if(isset($_POST['reserve_confirm_btn'])){
        try{
            $pdo = connect();
            $pdo->beginTransaction();
            foreach($selectedTimeRanges as $selectedTimeRangeID){
                $statment = $pdo->prepare('
                    INSERT INTO reserved_info 
                        (
                            reserve_schedule_year, 
                            reserve_schedule_month, 
                            reserve_schedule_day, 
                            reserve_people_cnt, 
                            contact_phone_number, 
                            contact_mail_address, 
                            utilization_range_id,
                            users_table_id
                        )
                    VALUES
                        (
                            :reserve_schedule_year, 
                            :reserve_schedule_month, 
                            :reserve_schedule_day, 
                            :reserve_people_cnt, 
                            :contact_phone_number, 
                            :contact_mail_address, 
                            :utilization_range_id,
                            :users_table_id
                        )
                ');
                $statment->bindValue(':reserve_schedule_year',  $selectedYear,            PDO::PARAM_INT);
                $statment->bindValue(':reserve_schedule_month', $selectedMonth,           PDO::PARAM_INT);
                $statment->bindValue(':reserve_schedule_day',   $selectedDay,             PDO::PARAM_INT);
                $statment->bindValue(':reserve_people_cnt',     $userNum,                 PDO::PARAM_INT);
                $statment->bindValue(':contact_phone_number',   $contactPhoneNumber,      PDO::PARAM_STR);
                $statment->bindValue(':contact_mail_address',   $contactMailAddress,      PDO::PARAM_STR);
                $statment->bindValue(':utilization_range_id',   $selectedTimeRangeID + 1, PDO::PARAM_INT); // DB上でのIDとコード上でのナンバリングのつじつま合わせのため数値を+1オフセットしている
                $statment->bindValue(':users_table_id',         $usersTableId,            PDO::PARAM_INT);
                $statment->execute();
            }
            $pdo->commit();

        } catch(PDOException $e) {
            $pdo->rollBack();
            escapePageToErrPage();
        }
        escapePageToReserveSuccessPage();
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
<p class="lead-text">下記の内容で予約を確定します。</p>
<div class="reserve-confirm-wrapper">
    <form action="" method="post">
        <div class="reserve-confirm-wrapper__date disp-grid grid-just-center">
            <p>ご希望日</p>
            <div>
                <span><?php echo $selectedYear; ?></span><span>年</span>
                <span><?php echo $selectedMonth; ?></span><span>月</span>
                <span><?php echo $selectedDay; ?></span><span>日</span>
            </div>
        </div>
        <div class="reserve-confirm-wrapper__order-times disp-grid grid-just-center">
            <p>ご利用時間</p>
            <div>
                <?php foreach($selectedTimeRanges as $selectedTimeRangeID): ?>
                    <p><?php echo escapeHtml($reserveTimeRangeList[$selectedTimeRangeID]); ?></p>
                <?php endforeach ?>
            </div>
        </div>
        <div class="reserve-confirm-wrapper__person-num disp-grid grid-just-center">
            <p>ご利用人数</p>
            <p><?php echo escapeHtml((string)$userNum) . "名"; ?></p>
        </div>
        <div class="reserve-confirm-wrapper__person-name disp-grid grid-just-center">
            <p>氏名</p>
            <p><?php echo escapeHtml($_SESSION['user_info']['first_name']) . escapeHtml($_SESSION['user_info']['last_name']) . " 様"; ?></p>
        </div>
        <div class="reserve-confirm-wrapper__person-tel disp-grid grid-just-center">
            <p>電話番号（連絡先）</p>
            <p><?php echo escapeHtml($contactPhoneNumber); ?></p>
        </div>
        <div class="reserve-confirm-wrapper__person-email disp-grid grid-just-center">
            <p>E-mail（連絡先）</p>
            <p><?php echo escapeHtml($contactMailAddress); ?></p>
        </div>
        <button class="reserve-confirm-wrapper__btn action-btn btn-color-confirm" type="submit" name="reserve_confirm_btn">予約</button>
    </form>
    <div class="reserve-confirm-btn disp-grid grid-just-center">
        <a href="./reserve-inputInfo-page.php">情報入力画面へ戻る</a>
        <a href="./reserve-yearMonthCalender-page.php">カレンダーページへ</a>
    </div>
</div>