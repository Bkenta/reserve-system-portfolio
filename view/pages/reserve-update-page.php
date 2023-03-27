<?php 
    declare (strict_types=1);

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
    if(count($_SESSION['updateTgtInfos']) < 0) escapePageToHomeMenu();

    // 「予約内容確認」ページ以外からの流入はアクセス禁止
    if(!isset($_SESSION['updateTgtInfos']['updateTgtRecordID'])) escapePageToHomeMenu();

    // 「戻る」ボタン押下時、使用していたセッション変数を破棄する
    if(isset($_POST['to-reserveCondition-page-btn'])){
        unset($_SESSION['updateTgtInfos']);
        unset($_SESSION['infoDisplayRecordNums']);
        
        escapePageToReserveConditionPage();
    }

    // 更新対象となるブラウザ表示用行番号
    if(isset($_SESSION['updateTgtInfos']['updateTgtRecordID'])) $updateTgtRecordId = $_SESSION['updateTgtInfos']['updateTgtRecordID'];
    
    // 「更新」ボタン押下時の処理
    // レコードの更新
    if(isset($_POST['to-reserveConfirm-page-btn'])){
        /*******************************************************
         * バリデ―ション処理 *
         * -----------------------------------------------------
         * 利用人数     ……  1～8の数字のみ
         * 電話番号     ……  0から始まる4桁以下-4桁-4桁の数字のみ
         * e-mail       ……  e-mail形式のみ
         *******************************************************/
        if(validateUserNum($_POST['user-num']))             $errMsg['notUserNum'] = '※人数が不正です。';
        if(validateTel(    $_POST['contact-phone-number'])) $errMsg['notTel']     = '※電話番号の入力形式が正しくありません。<br>（例）080-1234-3456';
        if(validateMail(   $_POST['contact-mailAddress']))  $errMsg['notMail']    = '※E-mailの入力形式が正しくありません。<br>（例）example@gmail.co.jp';

        // バリデーションチェックをすべてクリアしていれば更新処理を実行
        if(!isset($errMsg)){
            // 更新対象レコードID（DBレコードのID）
            $updateTgtRecordDBIDs = $_SESSION['infoDisplayRecordNums'][$updateTgtRecordId];
            // 配列に変換
            $updateTgtRecordDBIDsArray = explode(',', $updateTgtRecordDBIDs);

            // DBデータ更新処理
            try {
                $pdo = connect();
                $pdo->beginTransaction();

                // 予約済みの時間枠すべてに対して更新処理を実行
                foreach($updateTgtRecordDBIDsArray as $updateRecordId){
                    $statment = $pdo->prepare('
                        UPDATE 
                            reserved_info 
                        SET 
                            reserve_people_cnt   = :reserve_people_cnt,
                            contact_phone_number = :contact_phone_number,
                            contact_mail_address = :contact_mail_address
                        WHERE
                            id = :recordID
                        ;
                    ');
                    $statment->bindValue(':reserve_people_cnt',   $_POST['user-num'],             PDO::PARAM_INT);
                    $statment->bindValue(':contact_phone_number', $_POST['contact-phone-number'], PDO::PARAM_STR);
                    $statment->bindValue(':contact_mail_address', $_POST['contact-mailAddress'],  PDO::PARAM_STR);
                    $statment->bindValue(':recordID',             $updateRecordId,                PDO::PARAM_INT);  // 更新対象のレコードID
                    $statment->execute();
                }
                $pdo->commit();

            } catch(PDOException $e) {
                $pdo->rollBack();
                escapePageToErrPage();
            } 
            // セッション変数の更新
            $_SESSION['updateTgtInfos']['reservePeopleCnt']   = $_POST['user-num'];
            $_SESSION['updateTgtInfos']['contactPhoneNumber'] = $_POST['contact-phone-number'];
            $_SESSION['updateTgtInfos']['contactMailAddress'] = $_POST['contact-mailAddress'];

            // 不要となったセッションの破棄
            unset($_SESSION['infoDisplayRecordNums']);
            
            // 成功ページへ遷移
            escapePageToReserveUpdateSuccessPage();
        }
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

<p class="lead-text">※ご予約日・ご予約時間の変更をご希望の場合は、一度キャンセルをしてから改めてご予約下さい。</p>

<!-- エラーメッセージ -->
<div class="wrapper">
    <?php if(isset($errMsg)):?>
        <?php foreach($errMsg as $msg): ?>
            <p class="err-msg"><?=$msg?></p>
        <?php endforeach ?>
    <?php endif ?>
</div>

<div class="reserve-update-wrapper">
    <form action="" method="post">
        <div class="reserve-update-wrapper__date disp-grid grid-just-center">
            <p>予約日</p>
            <p><?php echo escapeHtml($_SESSION['updateTgtInfos']['reserveYear'])  . "年" 
                        . escapeHtml($_SESSION['updateTgtInfos']['reserveMonth']) . "月" 
                        . escapeHtml($_SESSION['updateTgtInfos']['reserveDay'])   . "日"; ?>
            </p>
        </div>
        <div class="reserve-update-wrapper__order-times disp-grid grid-just-center">
            <p>予約時間</p>
            <div>
                <?php $reservedTimeRangeIDs = explode(",", $_SESSION['updateTgtInfos']['reserveTimeRanges']); ?>
                <?php foreach($reservedTimeRangeIDs as $utilization_range_id): ?>
                    <p><?php echo escapeHtml($reserveTimeRangeList[ $utilization_range_id - 1 ]); ?></p>
                <?php endforeach ?>
            </div>
        </div>
        <div class="reserve-update-wrapper__person-num disp-grid grid-just-center">
            <p>利用人数</p>
            <p>
                <select name="user-num">
                    <?php $reservedCnt = (int)$_SESSION['updateTgtInfos']['reservePeopleCnt']; ?>
                    <option value="1" <?php echo $reservedCnt == 1 ? "selected" : ""; ?>>1名</option>
                    <option value="2" <?php echo $reservedCnt == 2 ? "selected" : ""; ?>>2名</option>
                    <option value="3" <?php echo $reservedCnt == 3 ? "selected" : ""; ?>>3名</option>
                    <option value="4" <?php echo $reservedCnt == 4 ? "selected" : ""; ?>>4名</option>
                    <option value="5" <?php echo $reservedCnt == 5 ? "selected" : ""; ?>>5名</option>
                    <option value="6" <?php echo $reservedCnt == 6 ? "selected" : ""; ?>>6名</option>
                    <option value="7" <?php echo $reservedCnt == 7 ? "selected" : ""; ?>>7名</option>
                    <option value="8" <?php echo $reservedCnt == 8 ? "selected" : ""; ?>>8名</option>
                </select>
            </p>
        </div>
        <div class="reserve-update-wrapper__person-name disp-grid grid-just-center">
            <p>氏名</p>
            <p><?php echo escapeHtml($_SESSION['user_info']['first_name']) . escapeHtml($_SESSION['user_info']['last_name']) . " 様"; ?></p>
        </div>
        <div class="reserve-update-wrapper__person-tel disp-grid grid-just-center">
            <p>電話番号（連絡先）</p>
            <p><input type="text" required name="contact-phone-number" value="<?php echo escapeHtml($_SESSION['updateTgtInfos']['contactPhoneNumber']); ?>"></p>
        </div>
        <div class="reserve-update-wrapper__person-email disp-grid grid-just-center">
            <p>E-mail（連絡先）</p>
            <p><input type="text" name="contact-mailAddress" value="<?php echo escapeHtml($_SESSION['updateTgtInfos']['contactMailAddress']); ?>"></p>
        </div>
        <div class="reserve-update-wrapper__btn disp-grid grid-just-center">
            <button class="action-btn btn-color-confirm" type="submit" name="to-reserveConfirm-page-btn">変更</button>
            <button class="action-btn btn-color-backpage" type="submit" name="to-reserveCondition-page-btn">戻る</button>
        </div>
    </form>
</div>
