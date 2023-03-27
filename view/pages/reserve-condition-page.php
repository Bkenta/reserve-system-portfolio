<?php 
    declare(strict_types=1);

    try{
        require_once (dirname(__FILE__, 2) . '/components/header/header.php' );
        require_once (dirname(__FILE__, 3) . '/app/functions/db/dbConnection.php');
        require_once (dirname(__FILE__, 3) . '/app/functions/validate/escape.php');
        require_once (dirname(__FILE__, 3) . '/app/functions/reserveSysCtr/conditionCtr.php');
        require_once (dirname(__FILE__, 3) . '/app/constants/reserveTimeRangeData.php');       // $reserveTimeRangeList読み込み    
    
    } catch(Exception $e) {
        escapePageToErrPage();
    }

    // 「内容変更」ボタンからの流入
    if(isset ($_POST['to-updateReservedInfo-page'])){

        // *** ラジオボタンに割り振られているIDが不正に変更されていないかチェック ***
        // 存在しうる更新対象のデータ件数
        if(isset($_SESSION['infoDisplayRecordNums'])) $dataCnt = count($_SESSION['infoDisplayRecordNums']);
        // 内容確認ページ上で変更対象valueの偽装検証
        // ※更新対象レコードの件数よりも多い、もしくは0やマイナスの数値が入ってきた場合は偽装と判断
        $updateTgtRecordId = (int)$_POST['tgt-recordID'];
        if($dataCnt < $updateTgtRecordId || $updateTgtRecordId <= 0) escapePageToHomeMenu();
        
        $_SESSION['updateTgtInfos'] = array(
            'updateTgtRecordID'  => $_POST['tgt-recordID'],
            'reserveYear'        => $_POST['reserve-year'         . (string)$_POST['tgt-recordID']],
            'reserveMonth'       => $_POST['reserve-month'        . (string)$_POST['tgt-recordID']],
            'reserveDay'         => $_POST['reserve-day'          . (string)$_POST['tgt-recordID']],
            'reserveTimeRanges'  => $_POST['reserve-time-ranges'  . (string)$_POST['tgt-recordID']],
            'reservePeopleCnt'   => $_POST['reserve-people-cnt'   . (string)$_POST['tgt-recordID']],    // $_POST['tgt-recordID'] = 表示用行番号
            'contactPhoneNumber' => $_POST['contact-phone-number' . (string)$_POST['tgt-recordID']],
            'contactMailAddress' => $_POST['contact-mail-address' . (string)$_POST['tgt-recordID']]
        );
        escapePageToReserveUpdatePage();
    }

    // 「キャンセル」ボタンからの流入
    if(isset ($_POST['to-deleteReservedInfo-page'])){
        $_SESSION['deleteTgtInfos'] = array(
            'deleteTgtRecordID'  =>  $_POST['tgt-recordID'],
            'reserveYear'        =>  $_POST['reserve-year'         . (string)$_POST['tgt-recordID']],
            'reserveMonth'       =>  $_POST['reserve-month'        . (string)$_POST['tgt-recordID']],
            'reserveDay'         =>  $_POST['reserve-day'          . (string)$_POST['tgt-recordID']],
            'reserveTimeRanges'  =>  $_POST['reserve-time-ranges'  . (string)$_POST['tgt-recordID']],
            'reservePeopleCnt'   =>  $_POST['reserve-people-cnt'   . (string)$_POST['tgt-recordID']],    // $_POST['tgt-recordID'] = 表示用行番号
            'contactPhoneNumber' =>  $_POST['contact-phone-number' . (string)$_POST['tgt-recordID']],
            'contactMailAddress' =>  $_POST['contact-mail-address' . (string)$_POST['tgt-recordID']]
        );
        escapePageToReserveDeletePage();
    }
    
    // TODO:「予約日を過ぎた予約データをすべて破棄する処理」が実装できていない

    // データベースから予約状況セット    
    try {
        $pdo = connect();
        $pdo->beginTransaction();

        $statment = $pdo->prepare('
            SELECT 
                GROUP_CONCAT(id order by id) as record_ids,
                reserve_schedule_year, 
                reserve_schedule_month, 
                reserve_schedule_day, 
                reserve_people_cnt, 
                contact_phone_number, 
                contact_mail_address, 
                GROUP_CONCAT(utilization_range_id order by utilization_range_id) as utilization_range_ids
            FROM
                reserved_info
            WHERE
                users_table_id = :users_table_id
                GROUP BY
                reserve_schedule_year, 
                reserve_schedule_month, 
                reserve_schedule_day
            ORDER BY 
                reserve_schedule_year ASC, 
                reserve_schedule_month ASC, 
                reserve_schedule_day ASC
            ;
        ');
        $statment->bindValue(':users_table_id', $_SESSION['user_info']['users_table_id'] ,PDO::PARAM_INT);
        $statment->execute();
        $reservedInfoSets = $statment->fetchAll();

        $pdo->commit();

    } catch(PDOException $e) {
        $pdo->rollBack();
        escapePageToErrPage();
    }

    // セッションに「ユーザが選択可能なラジオボタンID」と「DBから取得した日付別のレコードID」を紐づけした配列を格納
    // 「更新ページ」でデータ改ざんチェックを行う為に使用する
    if(count($reservedInfoSets) !== 0){
        unset($_SESSION['infoDisplayRecordNums']);
        $_SESSION['infoDisplayRecordNums'] = linkSelectedRadioIdAndReservedInfosAboutDataOnDB($reservedInfoSets);
    } else {
        $nothingMsg = '現在、予約情報はありません。';
    }
?>
<?php 
    // タイトル・ログアウトボタンの読み込み
    require_once(dirname(__FILE__, 2) . '/components/header/header-under-content.php');
?>

<div class="wrapper">
    <?php if(isset($nothingMsg)): ?>
        <p class="err-msg"><?=$nothingMsg?></p>
        <a href="./homeMenu.php">ホームへ戻る</a>
    <?php else: ?>    
        <p class="lead-text">変更、またはキャンセルしたい予約情報をお選び下さい。</p>
    <?php endif ?>
</div>
<div class="reserve-condition-wrapper">
    <form action="" method="POST">
        <!-- DB対象レコード特定に使用 -->
        <?php $infoDisplayRecordNum  = 0; ?>
        <?php foreach($reservedInfoSets as $reservedInfos): ?>
            <?php $infoDisplayRecordNum += 1; ?>
            <div class="reserve-condition-wrapper__date disp-grid grid-just-center">
                <p>予約日</p>
                <div>
                    <input type="hidden" name=<?="reserve-year"  . (string)$infoDisplayRecordNum; ?> value="<?php echo escapeHtml((string)$reservedInfos['reserve_schedule_year']); ?>">
                    <input type="hidden" name=<?="reserve-month" . (string)$infoDisplayRecordNum; ?> value="<?php echo escapeHtml((string)$reservedInfos['reserve_schedule_month']); ?>">
                    <input type="hidden" name=<?="reserve-day"   . (string)$infoDisplayRecordNum; ?> value="<?php echo escapeHtml((string)$reservedInfos['reserve_schedule_day']); ?>">
                    <?php echo escapeHtml((string)$reservedInfos['reserve_schedule_year']) . "年" . escapeHtml((string)$reservedInfos['reserve_schedule_month']) . "月" . escapeHtml((string)$reservedInfos['reserve_schedule_day']) . "日"; ?>
                </div>
            </div>
            <div class="reserve-condition-wrapper__box disp-grid grid-just-center">
                <input type="radio" name="tgt-recordID" value="<?php echo $infoDisplayRecordNum; ?>" <?php echo $infoDisplayRecordNum==1 ? "checked" : ""; ?>>
                <div class="reserve-condition-wrapper__box__form-area">
                    <div class="reserve-condition-wrapper__order-times disp-grid grid-just-center">
                        <p>利用時間</p>
                        <div>
                            <?php $reservedInfosArray = explode(",", $reservedInfos['utilization_range_ids'])?>
                            <?php foreach($reservedInfosArray as $utilization_range_id): ?>
                                <input type="hidden" name=<?="reserve-time-ranges" . (string)$infoDisplayRecordNum; ?> value="<?php echo escapeHtml($reservedInfos['utilization_range_ids']); ?>">
                                <p><?php echo escapeHtml($reserveTimeRangeList[ $utilization_range_id - 1 ]); ?></p>
                            <?php endforeach ?>
                        </div>
                    </div>
                    <div class="reserve-condition-wrapper__person-num disp-grid grid-just-center">
                        <p>利用人数</p>
                        <p><input type="text" name=<?php echo "reserve-people-cnt" . (string)$infoDisplayRecordNum; ?> value="<?php echo escapeHtml((string)$reservedInfos['reserve_people_cnt']); ?>" readonly>名様</p>
                    </div>
                    <div class="reserve-condition-wrapper__person-tel disp-grid grid-just-center">
                        <p>連絡先（電話番号）</p>
                        <p><input type="text" name=<?php echo "contact-phone-number" . (string)$infoDisplayRecordNum; ?> value="<?php echo escapeHtml($reservedInfos['contact_phone_number']); ?>" readonly></p>
                    </div>
                    <div class="reserve-condition-wrapper__person-email disp-grid grid-just-center">
                        <p>連絡先（メールアドレス）</p>
                        <p><input type="text" name=<?php echo "contact-mail-address" . (string)$infoDisplayRecordNum; ?> value="<?php echo escapeHtml($reservedInfos['contact_mail_address']); ?>" readonly></p>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
        <?php if(!isset($nothingMsg)): ?>
            <div class="reserve-condition-wrapper__btn disp-grid grid-just-center">
                <button class="action-btn btn-color-next" type="submit" name="to-updateReservedInfo-page">内容変更</button>
                <button class="action-btn btn-color-denger" type="submit" name="to-deleteReservedInfo-page">キャンセル</button>
            </div>
        <?php endif ?>
    </form>

    <?php echo isset($nothingMsg) ? "" : "<a href=\"./homeMenu.php\">トップページ</a>"; ?>
</div>

