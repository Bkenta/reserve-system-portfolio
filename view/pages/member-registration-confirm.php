<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/escape.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/db/dbConnection.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/escapePage/escapeToOtherPage.php');

    } catch (Exception $e) {
        escapePageToErrPageBeforeLogin();
    }

    session_start();
    
    // URL入力による直アクセスは禁止
    if(!isset($_SESSION['user_info'])) escapePageToLogin();
    // // 「入力画面へ戻る」ボタン
    if(isset($_POST['back_to_registrationPage'])){
        $_SESSION['btn_action_flag']['back_to_registrationPage'] = true;    // 入力値を復元するためのトリガーとして使用
        escapePageToMemberRegistration();
    } 
        
    /**
     * 「登録」ボタンによる処理
     * 入力内容をDBに登録
     * 
     * 【新規会員情報】
     * user_id          ……  ユーザID
     * user_password    ……  ユーザパスワード
     * first_name       ……  苗字
     * last_name        ……  名前
     * age              ……  年齢
     * telphone_number  …… 電話番号 
     * mail_address     ……  メールアドレス
     */
    if(isset($_POST['confirm_registration'])){
        try {
            $pdo = connect();
            $pdo->beginTransaction();

            $userInfoStatment = $pdo->prepare('
                INSERT INTO users (
                        user_id, user_password, 
                        first_name, 
                        last_name, 
                        age, 
                        telphone_number, 
                        mail_address
                        ) 
                    VALUES (
                        :user_id, 
                        :user_password, 
                        :first_name, 
                        :last_name, 
                        :age, 
                        :telphone_number, 
                        :mail_address
                        )
            ');      
            
            // パスワード   ハッシュ化
            $user_password = password_hash($_SESSION['user_info']['user_password'], PASSWORD_DEFAULT, ['cost' => 13]);

            // 電話番号
            $telphoneNum = $_SESSION['user_info']['tel_first'] .'-'. $_SESSION['user_info']['tel_middle'] .'-'. $_SESSION['user_info']['tel_last'];

            $userInfoStatment->bindValue(':user_id',         $_SESSION['user_info']['user_id'],    PDO::PARAM_STR);
            $userInfoStatment->bindValue(':user_password',   $user_password,                       PDO::PARAM_STR);
            $userInfoStatment->bindValue(':first_name',      $_SESSION['user_info']['first_name'], PDO::PARAM_STR);
            $userInfoStatment->bindValue(':last_name',       $_SESSION['user_info']['last_name'],  PDO::PARAM_STR);
            $userInfoStatment->bindValue(':age',             $_SESSION['user_info']['age'],        PDO::PARAM_INT);
            $userInfoStatment->bindValue(':telphone_number', $telphoneNum,                         PDO::PARAM_STR);
            $userInfoStatment->bindValue(':mail_address',    $_SESSION['user_info']['user_mail'],  PDO::PARAM_STR);
            $userInfoStatment->execute();
            
            $pdo->commit();

            escapePageToMemberRegistrationSuccess();

        } catch (Exception $e) {
            $pdo->rollBack();
            escapePageToErrPageBeforeLogin();
        }
    }
?>
<head>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" type="text/css" href="/css/registration-member-style.css">
    <!-- インデックスするのを拒否 -->
    <meta name="robots" content="noindex">
</head>
<div class="wrapper">
    <div class="registration-confirm-page-title wrapper">
        <h3>以下の内容で登録します。</h3>
    </div>
    <form action="" method="POST">
        <div class="registration-confirm-form__wrapper wrapper disp-grid grid-just-center">
            <!-- 英数字のみ許可 -->
            <p>ユーザID</p><span><?php echo escapeHtml($_SESSION['user_info']['user_id']); ?></span>
            <p>名字</p><span><?php echo escapeHtml($_SESSION['user_info']['first_name']); ?></span>
            <p>名前</p><span><?php echo escapeHtml($_SESSION['user_info']['last_name']); ?></span>
            <p>年齢</p><span><?php echo escapeHtml($_SESSION['user_info']['age']); ?>歳</span>
            <p>電話番号</p>
            <div class="registration-confirm-form__wrapper__tel disp-grid">
                <div class="registration-confirm-form__wrapper__tel__element"><span><?php echo escapeHtml($_SESSION['user_info']['tel_first']); ?></span></div>
                <div><span>-</span></div>
                <div class="registration-confirm-form__wrapper__tel__element"><span><?php echo escapeHtml($_SESSION['user_info']['tel_middle']); ?></span></div>
                <div><span>-</span></div>
                <div class="registration-confirm-form__wrapper__tel__element"><span><?php echo escapeHtml($_SESSION['user_info']['tel_last']); ?></span></div>
            </div>
            <p>E-mail</p>
            <span><?php echo escapeHtml($_SESSION['user_info']['user_mail']); ?></span>
            <p>パスワード</p><span>個人情報保護のため非公開</span>
        </div>
        <div class="registration-confirm-form__btn disp-grid grid-just-center">
            <button class="btn-color-confirm action-btn registration-confirm-form__btn__confirm" type="submit" name="confirm_registration">登録</button>
            <button class="btn-color-backpage action-btn registration-confirm-form__btn__back-to-inputform" type="submit" name="back_to_registrationPage" value="back_to_registrationPage">入力画面へ戻る</button>
        </div>
    </form>
</div>
