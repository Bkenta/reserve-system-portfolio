<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/db/dbConnection.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/escape.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/escapePage/escapeToOtherPage.php');  
        require_once(dirname(__FILE__, 3) . '/app/functions/reserveSysCtr/timeRangeCtr.php');  
        require_once(dirname(__FILE__, 3) . '/app/constants/reserveTimeRangeData.php');       // $reserveTimeRangeList読み込み
          
    } catch(Exception $e) {
        escapePageToErrPage();
    }

    // URLによる直接アクセスを拒否
    if(!isset($_SESSION['reserve_info_data'])) escapePageToHomeMenu();

    // 選択された日付
    $selectedYear  = $_SESSION['reserve_info_data']['selected_year'];
    $selectedMonth = $_SESSION['reserve_info_data']['selected_month'];
    $selectedDay   = $_SESSION['reserve_info_data']['selected_day'];

    /* ******************************************************************************
     *                      予約時間セレクトフォームの生成
     *********************************************************************************/
    /**
     * １．選択された年月日をキーとしてDBから予約情報を取得
     */
    try {
        $pdo = connect();
        $pdo->beginTransaction();

        // 予約情報テーブルから該当日のレコードを抽出
        $statment = $pdo->prepare('
            SELECT utilization_range_id FROM reserved_info 
                WHERE 
                    reserve_schedule_year  = :year AND 
                    reserve_schedule_month = :month AND
                    reserve_schedule_day   = :day
        ');
        $statment->bindValue(':year',  $selectedYear,  PDO::PARAM_INT);
        $statment->bindValue(':month', $selectedMonth, PDO::PARAM_INT);
        $statment->bindValue(':day',   $selectedDay,   PDO::PARAM_INT);
        $statment->execute();

        // 抽出したレコードから予約済み枠を取得
        $reservedConditionSets = $statment->fetchAll();
        $pdo->commit();

    } catch(PDOException $e) {
        $pdo->rollBack();
        escapePageToErrPage();
    }

    /*
     * ２．予約情報が見つかった場合、各時間枠に対して
     *          表示記号「×」
     *          cssプロパティ「hidden」
     *     を付与する
     * ※時間枠リスト（$reserveTimeRangeList）を別のデータファイルから読み込んで使用
     */
    // 予約可否表示 …… 初期値は「選択可能」という意味で「〇」を設定
    $browsInitCondition = '〇';
    $reserveBrowsConditionArray = initBrowsConditionArray($reserveTimeRangeList);
    // 予約時間チェックボックスstyle …… inputタグの表示・非表示設定（選択不可のものはhiddenで非表示）
    $inputerVisibleInitStyle = 'visible';
    $reserveInputerVisibleStyleArray = initInputerVisibleStyleArray($reserveTimeRangeList);
    
    /**
     * ３．下記２つの連想配列を元に、予約セレクトフォームを生成・出力する 
     * 該当時間枠がすでに予約済みなら選択不可とする
     * ◆reserveBrowsConditionArray      …… 予約可否表示 〇 or × を管理する
     * ◆reserveInputerVisibleStyleArray …… チェックボックスのvisible、hiddenを管理する
     */
    foreach($reservedConditionSets as $rangeID){ 
        $stringTimeRangeID = $rangeID['utilization_range_id'] - 1;        // 時間枠リスト（$reserveTimeRangeList）の要素ID
        $simpleTimeRange   = $reserveTimeRangeList[$stringTimeRangeID];   // 単一の時間枠

        $reserveBrowsConditionArray[$simpleTimeRange] = '×';              // 時間枠ごとの予約可否状況テキスト …… 〇 or ×
        $reserveInputerVisibleStyleArray[$simpleTimeRange]   = 'hidden;'; // 時間枠ごとのinputタグのstyle …… hidden or ''
    }
    /*********************************************************************************************** */

    // 「次へ」ボタン押下直後の処理
    if(isset($_POST['to_reserveInputInfo_page_btn'])){
        if(isset($_POST['reserved_time_range'])){
            // セッションに予約枠を格納
            $_SESSION['reserve_info_data']['selected_time_range'] = $_POST['reserved_time_range'];
            escapePageToReserveInputInfoPage();
        }
        // １つも時間が選択されていなければエラーメッセージ格納
        $errMsg = '予約したい時間を１つ以上選択してください。';
    }

    // 「戻る」ボタン押下時の処理
    if(isset($_POST['return_mainCalender_page_btn'])) escapePageToReserveYearMonthCalenderPage();
?>
<?php 
    // タイトル・ログアウトボタンの読み込み
    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header-under-content.php');

    } catch(Exception $e) {
        escapePageToErrPage();
    }
?>

<div class="time-range-calender-wrapper">
    <span class="year"><?php echo $selectedYear; ?></span><span>年</span>
    <span class="month"><?php echo $selectedMonth; ?></span><span>月</span>
    <span class="day"><?php echo $selectedDay; ?></span><span>日</span>

    <!-- エラーメッセージ -->
    <?php if(isset($errMsg)): ?>
        <p class="err-msg"><?php echo $errMsg; ?></p>
    <?php endif ?>

    <form action="" method="POST">
        <div class="time-range-table">
                <div class="time-range-table__head disp-grid grid-just-center grid-align-center">
                    <p>予約時間</p>
                    <p>予約可能状況</p>
                </div>
                <!-- 予約時間枠リストの表示
                以下の構造でHTMLを生成する
                例：12～14時を表示（ID＝2）
                <tr>
                    <td><input type="checkbox" name="reserved_time_range[]" value="2"></td>
                    <td>12～14時</td>
                    <td>〇</td>
                </tr>
                -->
                <?php for($timeRangeID = 0; $timeRangeID < count($reserveTimeRangeList); $timeRangeID++): ?>
                    <!-- 変数定義 -->
                    <?php $reserveRangeText = $reserveTimeRangeList[ $timeRangeID ];                    // 表示時間枠 ?>
                    <?php $inputerStyle     = $reserveInputerVisibleStyleArray[ $reserveRangeText ];    // inputタグのstyle …… visibilityHidden or "" ?>
                    <?php $reserveCondition = $reserveBrowsConditionArray[ $reserveRangeText ];         // 予約可否状況 …… 〇 or × ?>

                    <div class="time-range-table__time-range disp-grid grid-just-center grid-align-center">
                        <p><input type="checkbox" style="visibility: <?php echo $inputerStyle?>" name="reserved_time_range[]" value="<?php echo $timeRangeID; ?>"></p>
                        <p><?php echo $reserveRangeText; ?></p>
                        <p><?php echo $reserveCondition; ?></p>
                    </div>
                <?php endfor ?>
        </div> 
        <div class="time-range-table__btn disp-grid grid-just-center grid-align-center">
            <button class="action-btn btn-color-next" type="submit" name="to_reserveInputInfo_page_btn">次へ</button>
            <button class="action-btn btn-color-backpage" type="submit" name="return_mainCalender_page_btn">戻る</button>
        </div>
    </form>
</div>