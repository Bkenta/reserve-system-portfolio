<?php 
    declare(strict_types=1);
    
    try {
        require_once (dirname(__FILE__, 2) . '/components/header/header.php' );
        require_once (dirname(__FILE__, 3) . '/app/functions/db/dbConnection.php');
        require_once (dirname(__FILE__, 3) . '/app/functions/validate/escape.php');
        require_once (dirname(__FILE__, 3) . '/app/constants/reserveTimeRangeData.php');       // $reserveTimeRangeList読み込み
        require_once (dirname(__FILE__, 3) . '/app/functions/escapePage/escapeToOtherPage.php');    

    } catch(Exception $e) {
        escapePageToErrPage();
    }

    // URLによる直接流入はアクセス禁止
    if(!isset($_SESSION['deleteTgtInfos']['deleteTgtRecordID'])) escapePageToHomeMenu();

    // 「戻る」ボタン押下時、使用していたセッション変数を破棄する
    if(isset($_POST['to-reserveCondition-page-btn'])){
        unset($_SESSION['deleteTgtInfos']);
        unset($_SESSION['infoDisplayRecordNums']);
        
        escapePageToReserveConditionPage();
    }

    /**
     * 削除対象データの偽装判定処理
     */
    if(isset($_SESSION['deleteTgtInfos']['deleteTgtRecordID']) && isset($_SESSION['infoDisplayRecordNums']))
        $deleteTgtRecordId = $_SESSION['deleteTgtInfos']['deleteTgtRecordID']; // 更新対象となるブラウザ表示用行番号
        $reserveDataCnt = count($_SESSION['infoDisplayRecordNums']);           // 存在する予約データ件数
    // 内容確認ページ上で変更対象valueの偽装検証
    // ※存在する予約データ件数 < 削除対象データのIDが大きい 
    //      または 
    //   0や負の数値が入ってきた場合は偽装と判断し、トップページに強制転送
    if($reserveDataCnt < $deleteTgtRecordId || $deleteTgtRecordId <= 0) escapePageToHomeMenu();

    // 「キャンセル」ボタン押下時の処理
    // レコードの更新
    if(isset($_POST['to-delete-success-page-btn'])){

        // 更新対象レコードID（DBレコードのID）
        $deleteTgtRecordDBIDs = $_SESSION['infoDisplayRecordNums'][$deleteTgtRecordId];
        // 配列に変換
        $deleteTgtRecordDBIDsArray = explode(',', $deleteTgtRecordDBIDs);
    
        // DBデータ更新処理
        try {
            $pdo = connect();
            $pdo->beginTransaction();
    
            // 予約済みの時間枠すべてに対して削除処理を実行
            foreach($deleteTgtRecordDBIDsArray as $deleteRecordId){
                $statment = $pdo->prepare('
                    DELETE FROM
                        reserved_info 
                    WHERE
                        id = :recordID
                    ;
                ');
                // 更新対象のレコードID
                $statment->bindValue(':recordID', $deleteRecordId, PDO::PARAM_INT);
                $statment->execute();
            }
            $pdo->commit();

        } catch(PDOException $e) {
            $pdo->rollBack();
            escapePageToErrPage();
        } 

        // 不要となったセッションの破棄
        unset($_SESSION['infoDisplayRecordNums']);

        // 予約キャンセル成功ページへ遷移
        $_SESSION['delete_success_flag'] = true;
        escapePageToReserveDeleteSuccessPage();
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
<p class="lead-text">下記のご予約内容をキャンセルします。</p>
<div class="wrapper">
    <p class="success-msg-1 alert-msg">※一度キャンセルしたご予約情報は元に戻せません。</p>
    <p class="success-msg-2">ご希望の際は、改めて予約ページよりご予約頂きますようお願い致します。</p>
</div>
<div class="reserve-delete-wrapper">
    <form action="" method="post">
        <div class="reserve-delete-wrapper__date disp-grid grid-just-center">
            <p>予約日</p>
            <p><?php echo escapeHtml($_SESSION['deleteTgtInfos']['reserveYear']) 
                 . "年" . escapeHtml($_SESSION['deleteTgtInfos']['reserveMonth']) 
                 . "月" . escapeHtml($_SESSION['deleteTgtInfos']['reserveDay']) 
                 . "日"; ?>
            </p>
        </div>
        <div class="reserve-delete-wrapper__order-times disp-grid grid-just-center">
            <p>予約時間</p>
            <div>
                <?php $reservedTimeRangeIDs = explode(',', $_SESSION['deleteTgtInfos']['reserveTimeRanges']); ?>
                <?php foreach($reservedTimeRangeIDs as $utilization_range_id): ?>
                    <p><?php echo escapeHtml($reserveTimeRangeList[ $utilization_range_id - 1 ]); ?></p>
                <?php endforeach ?>
            </div>
        </div>
        <div class="reserve-delete-wrapper__person-num disp-grid grid-just-center">
            <p>利用人数</p>
            <p><?php echo (int)$_SESSION['deleteTgtInfos']['reservePeopleCnt']; ?>名</p>
        </div>
        <div class="reserve-delete-wrapper__person-name disp-grid grid-just-center">
            <p>氏名</p>
            <p><?php echo escapeHtml($_SESSION['user_info']['first_name']) . escapeHtml($_SESSION['user_info']['last_name']) . ' 様'; ?></p>
        </div>
        <div class="reserve-delete-wrapper__person-tel disp-grid grid-just-center">
            <p>電話番号（連絡先）</p>
            <p><?php echo escapeHtml($_SESSION['deleteTgtInfos']['contactPhoneNumber']); ?></p>
        </div>
        <div class="reserve-delete-wrapper__person-email disp-grid grid-just-center">
            <p>E-mail（連絡先）</p>
            <p><?php echo escapeHtml($_SESSION['deleteTgtInfos']['contactMailAddress']); ?></p>
        </div>
        <div class="reserve-delete-wrapper__btn disp-grid grid-just-center">
            <button class="action-btn btn-color-denger" type="submit" name="to-delete-success-page-btn" class="delete-confirm-btn">確定</button>
            <button class="action-btn btn-color-backpage" type="submit" name="to-reserveCondition-page-btn">戻る</button>
        </div>
    </form>
</div>