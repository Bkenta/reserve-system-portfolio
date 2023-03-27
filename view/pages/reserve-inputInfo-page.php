<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/db/dbConnection.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/escape.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/inputInfovalidate.php');
        require_once(dirname(__FILE__, 3) . '/app/constants/reserveTimeRangeData.php');       // $reserveTimeRangeList読み込み
        require_once(dirname(__FILE__, 3) . '/app/functions/escapePage/escapeToOtherPage.php');    

    } catch(Exception $e) {
        escapePageToErrPage();
    }

    // URLによる直接アクセスを拒否
    if(!isset($_SESSION['reserve_info_data']['selected_time_range'])) escapePageToHomeMenu();

    // 選択された時間枠の保持
    $selectedTimeRanges = $_SESSION['reserve_info_data']['selected_time_range'];
    // 予約情報
    $selectedYear   = $_SESSION['reserve_info_data']['selected_year'];
    $selectedMonth  = $_SESSION['reserve_info_data']['selected_month'];
    $selectedDay    = $_SESSION['reserve_info_data']['selected_day'];
    $firstName      = $_SESSION['user_info']['first_name'];
    $lastName       = $_SESSION['user_info']['last_name'];
    $telNumber      = $_SESSION['user_info']['telphone_number'];
    $mailAdress     = $_SESSION['user_info']['user_mail'];

    // 「確認」ボタン押下時の処理
    // 予約情報のセッションへの格納
    if(isset($_POST['to-reserveConfirm-page-btn'])) {

        /*******************************************************
         * バリデ―ション *
         * -----------------------------------------------------
         * 利用人数     ……  1～8の数字のみ
         * 電話番号     ……  0から始まる4桁以下-4桁-4桁の数字のみ
         * e-mail       ……  e-mail形式のみ
         *******************************************************/
        if(validateUserNum($_POST['user_num']))         $errMsg['notUserNum'] = "※人数が不正です。";
        if(validateTel($_POST['contact_phone_number'])) $errMsg['notTel']     = "※電話番号の入力形式が正しくありません。<br>（例）080-1234-3456";
        if(validateMail($_POST['contact_mailAddress'])) $errMsg['notMail']    = "※E-mailの入力形式が正しくありません。<br>（例）example@gmail.co.jp";

        // バリデーションチェックを通過していれば確認ページへ
        if(!isset($errMsg)){
            $_SESSION['reserve_info_data']['user_num']             = $_POST['user_num'];                  // 利用人数
            $_SESSION['reserve_info_data']['contact_phone_number'] = $_POST['contact_phone_number'];      // 連絡先（電話番号）
            $_SESSION['reserve_info_data']['contact_mailAddress']  = $_POST['contact_mailAddress'];       // 連絡先（E-mail）

            escapePageToReserveConfirmPage();
        }
    }
    // 「戻る」ボタン押下時の処理
    if(isset($_POST['to-timeRangeCalender-page-btn'])) escapePageToReserveTimeRangeCalenderPage();

    // TODO:選択された予約情報のレコードが存在しないことを再チェックする
?>
<?php 
    // タイトル・ログアウトボタンの読み込み
    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header-under-content.php');

    } catch(Exception $e) {
        escapePageToErrPage();
    }
?>

<p class="lead-text">ご利用人数とご連絡先をご確認の上、確認ページへお進み下さい。</p>

<!-- エラーメッセージ -->
<div class="wrapper">
    <?php if(isset($errMsg)):?>
        <?php foreach($errMsg as $msg): ?>
            <p class="err-msg"><?php echo $msg; ?></p>
        <?php endforeach ?>
    <?php endif ?>
</div>
<div class="reserve-info-input-wrapper">
    <form action="" method="post">
        <div class="reserve-info-input-wrapper__date disp-grid grid-just-center">
            <p>希望日</p>
            <div>
                <span><?php echo $selectedYear; ?></span><span>年</span>
                <span><?php echo $selectedMonth; ?></span><span>月</span>
                <span><?php echo $selectedDay; ?></span><span>日</span>
            </div>
        </div>
        <div class="reserve-info-input-wrapper__order-times disp-grid grid-just-center">
            <p>利用時間</p>
            <div>
                <?php foreach($selectedTimeRanges as $selectedTimeRangeID): ?>
                    <p><?php echo $reserveTimeRangeList[$selectedTimeRangeID]; ?></p>
                <?php endforeach ?>
            </div>
        </div>
        <div class="reserve-info-input-wrapper__person-num disp-grid grid-just-center">
            <p>利用人数</p>
            <p>
                <select name="user_num">
                    <option value="1">1名</option>
                    <option value="2">2名</option>
                    <option value="3">3名</option>
                    <option value="4">4名</option>
                    <option value="5">5名</option>
                    <option value="6">6名</option>
                    <option value="7">7名</option>
                    <option value="8">8名</option>
                </select>
            </p>
        </div>
        <div class="reserve-info-input-wrapper__person-name disp-grid grid-just-center">
            <p>氏名</p>
            <p><?php echo escapeHtml($firstName) . escapeHtml($lastName) . " 様"; ?></p>
        </div>
        <div class="reserve-info-input-wrapper__person-tel disp-grid grid-just-center">
            <p>電話番号（連絡先）</p>
            <p><input type="text" required name="contact_phone_number" value="<?php echo escapeHtml($telNumber); ?>"></p>
        </div>
        <div class="reserve-info-input-wrapper__person-email disp-grid grid-just-center">
            <p>E-mail（連絡先）</p>
            <p><input type="text" required name="contact_mailAddress" value="<?php echo escapeHtml($mailAdress); ?>"></p>
        </div>
        <div class="reserve-info-input-wrapper__btn disp-grid grid-just-center">            
            <button class="action-btn btn-color-next" type="submit" name="to-reserveConfirm-page-btn">確認</button>
            <button class="action-btn btn-color-backpage" type="submit" name="to-timeRangeCalender-page-btn">戻る</button>
        </div>
    </form>
    <a href="./reserve-yearMonthCalender-page.php">カレンダーページへ</a>
</div>