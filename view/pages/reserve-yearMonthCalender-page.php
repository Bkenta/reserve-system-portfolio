<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/escape.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/calender/calender.php');

    } catch(Exception $e) {
        escapePageToErrPage();
    }

    // ページを開いた時の初期年月
    if(!isset($_POST['user_select_date'])){
        $month = (int)date('n');
        $year  = (int)date('Y');
    }
    
    // 日付が選択されたときに表示中の年月を格納
    if(isset($_POST['user_select_date'])){
        $month = (int)$_POST['selectedMonth'];
        $year  = (int)$_POST['selectedYear'];
    }

    /**
     * 予約時間枠カレンダーから「戻る」ボタンで戻ってきたとき
     * $year,$monthにセッション変数＝ユーザが最後に選択した年月を格納
     * 年月日に関わるセッション変数をリセット
     */
    if(isset($_POST['return_mainCalender_page_btn'])){
        $year  = (int)$_SESSION['reserve_info_data']['selected_year'];
        $month = (int)$_SESSION['reserve_info_data']['selected_month'];

        $_SESSION['reserve_info_data']['selected_year']  = '';
        $_SESSION['reserve_info_data']['selected_month'] = '';
        $_SESSION['reserve_info_data']['selected_day']   = '';
    }

    // 各ボタンからPOSTされた値の取得
    isset($_POST['prevMonth']) ? $prevMonth = (int)$_POST['prevMonth'] : $prevMonth = 9999;
    isset($_POST['prevYear'])  ? $prevYear  = (int)$_POST['prevYear']  : $prevYear  = 9999;
    isset($_POST['nextMonth']) ? $nextMonth = (int)$_POST['nextMonth'] : $nextMonth = 9999;
    isset($_POST['nextYear'])  ? $nextYear  = (int)$_POST['nextYear']  : $nextYear  = 9999;

    /**
     * 年月を切り替えるボタンの処理
     * ◆ 前月が0月となる場合は前年の12月を表示
     * ◆ 翌月が13月となる場合は翌年の1月を表示
     */
    if(isset($_POST['prevMonth'])){
        $pairMonthAndYear = setPrevMonthAndYear($prevMonth, $prevYear);
        $month = $pairMonthAndYear['month'];
        $year  = $pairMonthAndYear['year'];
    }
    if(isset($_POST['nextMonth'])){
        $pairMonthAndYear = setNextMonthAndYear($nextMonth, $nextYear);
        $month = $pairMonthAndYear['month'];
        $year  = $pairMonthAndYear['year'];
    }

    /********************************************************************************
     * 予約可能開始日に基づくカレンダー生成
     * ◆ 予約可能開始日は、$reservableOffsetNumに基づいて設定
     * ◆ それ以前の日付は選択自体を不可とする
     ********************************************************************************/ 
    // 今日の年月日情報の設定
    $todate  = new DateTime();
    $toYear  = (int)$todate->format('Y');
    $toMonth = (int)$todate->format('n');
    $today   = (int)$todate->format('j');

    /**
     * １．予約可能となる日付の生成
     */ 
    // 何日後から予約可能とするかの設定   （例）今日が17日なら20日から予約可能
    $reservableOffsetNum = 3;
    // 予約が可能となる年月日情報
    $reservableDate = new DateTime();
    $reservableDate->setDate($toYear, $toMonth, $today + $reservableOffsetNum); // 予約可能開始 年月日
    $reservableYear  = (int)$reservableDate->format('Y');                       // 予約可能開始 年
    $reservableMonth = (int)$reservableDate->format('n');                       // 予約可能開始 月

    /**
     * ２．「予約が可能となる日付」が属する「月」を起点としてカレンダーの日付配列を生成
     * 
     * 以下の関係になる場合は、「予約が可能となる日付を持つ年月」を元にカレンダーを生成
     *        生成しようとしているカレンダーの年月 < 「予約が可能となる日付」の年月
     * ※ページ初期表示において、現在が月末に近い場合、
     * 　今月の日付がすべて選べないにも関わらず表示されてしまうのを防ぐ為に条件を設けている
     */
    // ページ初期表示時に表示する年月
    $displayYearMonth = new DateTime();
    $displayYearMonth->setDate((int)$year, (int)$month, 1);

    // 条件：生成しようとしているカレンダーの年月 < 「予約が可能となる日付」の年月
    if($displayYearMonth < $reservableDate){
        $month = (int)$reservableMonth;
        $year  = (int)$reservableYear;
    }
    // カレンダーの日付配列の生成
    $calenderDates = createYearlyCalender((int)$month, (int)$year);

    /**
     * ３．「予約が可能となる日付」以前の日付は選択不可とする
     */
    // 日付の選択可否状況を表すフラグ（trueなら選択可能）
    $ableSelectDay = true;

    // 日付配列の各要素に選択可否フラグを付与
    foreach($calenderDates as $date){
        // 「カレンダーに表示する日付配列」に$ableSelectDayフラグを付加した配列
        $calenderDatesHasOption[] = array(
            "date"             => $date,
            "dispCalenderFlag" => $ableSelectDay
        );
    }

    // 最終的にカレンダーに表示する日付の配列（表示・非表示の設定済み）
    $dateOnDispCalender = new DateTime();
    // 「予約が可能となる日付」以前のものは「選択不可」とする
    foreach($calenderDatesHasOption as &$calenderDate){   // ループを回しながら配列を更新するために参照渡ししている
        // ついたちの表示位置のためのオフセットによる空日付をスキップ
        if($calenderDate['date'] == "") continue;

        // カレンダーに表示する日付を取得
        $dateOnDispCalender->setDate((int)$year, (int)$month, (int)$calenderDate['date']);

        // カレンダーに表示する日付 < 予約が可能となる日付ならばその日付は選択不可とする  
        if($dateOnDispCalender->getTimestamp() < $reservableDate->getTimestamp()) $calenderDate['dispCalenderFlag'] = false;
    }

    /**
     * 表示中の年月が「今日」と同じ年月なら「前月」ボタンは非表示とする
     */
    // 前月ボタンの表示制御フラグ   初期設定は「表示」
    $isDispPrevMonthBtn = true;

    // 今日の年月と表示中の年月が同じなら「前月」ボタンは非表示
    // 年月の比較の際、型（int or string）までは考慮しない
    if ((int)$reservableMonth === (int)$month && (int)$reservableYear === (int)$year) $isDispPrevMonthBtn = false;

    /****************************** 予約可能開始日に基づくカレンダー生成 ここまで ******************************/ 

    /**
     * 日付が選択されたら時間セレクトページへ遷移する
     */ 
    if(isset($_POST['user_select_date'])){
        $day = $_POST['user_select_date'];

        // 選択された日付をセッションに格納
        $_SESSION['reserve_info_data'] = array(
            'selected_year'  => (int)$year,
            'selected_month' => (int)$month,
            'selected_day'   => (int)$day
        );
        escapePageToReserveTimeRangeCalenderPage();
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
<p class="lead-text calender-lead-text">ご希望の日付をクリックして下さい。</p>
<div class="calender-wrapper">
    <?php require_once(dirname(__FILE__, 2) . '/components/calender/display-year-month.php'); ?>
    <?php require_once(dirname(__FILE__, 2) . '/components/calender/view-yearly-calender.php'); ?>
    <?php require_once(dirname(__FILE__, 2) . '/components/calender/btn-change-prev-next.php'); ?>
</div>