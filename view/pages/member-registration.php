<?php
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 3) . '/app/functions/getter/getUserInfoTypePostArray.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/db/dbConnection.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/escape.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/inputInfovalidate.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/escapePage/escapeToOtherPage.php');

    } catch(Exception $e) {
        escapePageToErrPageBeforeLogin();
    }

    // 「戻る」ボタンが押下されたら前のページ（ログインページ）へ戻る
    if(isset($_POST['member_registration_return_btn'])) escapePageToLogin();

    session_start();

    // 当ページの「確認」ボタンからのPOSTの場合
    if(isset($_POST['member_registration_btn']) && $_POST['member_registration_btn'] === 'send_registration_info'){
        // 全項目が入力されているかチェック
        if( empty($_POST['user_id']) || 
            empty($_POST['first_name']) || empty($_POST['last_name']) || 
            empty($_POST['age']) || 
            empty($_POST['tel_first']) || empty($_POST['tel_middle']) || empty($_POST['tel_last']) || 
            empty($_POST['user_mail']) || 
            empty($_POST['user_password']) || empty($_POST['user_password_check'])
            )
        {
            $errMsg['noAllInput'] = '全項目の入力が必須です。';
            $userInfo = getUserInfoTypePostArray();
        }
        
        /***********************************************
         * バリデ―ション *
         * ---------------------------------------------
         * ユーザID         ……  半角英数字のみ※8文字以上
         * 年齢            ……  数字のみ※15歳以上
         * 電話番号         ……  4桁 4桁 4桁の数字のみ
         * e-mail          ……  e-mail形式のみ
         * パスワード       ……  半角英数字のみ※8文字以上
         * ユーザIDの存在チェック
         * 確認パスワードのミスマッチチェック
         ***********************************************/
        if(validateUserId($_POST['user_id'])) 
            $errMsg['notUserId']            = '※ユーザIDは8文字以上の半角英数字で入力して下さい。';
        if(validateAge($_POST['age'])) 
            $errMsg['notAge']               = '※15歳以上の方のみ登録が可能です。';
        if(validateSplitTel($_POST['tel_first'], $_POST['tel_middle'], $_POST['tel_last'])) 
            $errMsg['notTel']               = '※電話番号の入力形式が正しくありません。';
        if(validateMail($_POST['user_mail'])) 
            $errMsg['notMail']              = '※E-mailの入力形式が正しくありません。<br>（例）example@gmail.co.jp';
        if(validatePassword($_POST['user_password'])) 
            $errMsg['notPassword']          = '※パスワードは8文字以上の半角英数字で入力して下さい。';
        if(noExistId($_POST['user_id'])) 
            $errMsg['idAlreadyExistMsg']    = '※入力されたIDはすでに存在しています。別のIDをお試し下さい。';
        if(mismatchConfirmPass($_POST['user_password'], $_POST['user_password_check'])) 
            $errMsg['passMismatchMsg']      = '※入力されたパスワードが一致しません。';
        
        // 上記チェックをすべてクリアしていたら確認画面へ遷移
        if(!isset($errMsg)){
            
            // 入力内容をセッションとして保持
            $_SESSION['user_info'] = getUserInfoTypePostArray();

            escapePageToMemberRegistrationConfirm();
        }

        // バリデーションチェックに引っかかった場合に入力内容を復元するための処理
        $userInfo = getUserInfoTypePostArray();
        
    //確認ページの「戻る」ボタンからのPOSTの場合 
    } elseif(isset($_SESSION['btn_action_flag']['back_to_registrationPage'])){
        $userInfo = [
            'user_id'             => $_SESSION['user_info']['user_id'],
            'user_password'       => $_SESSION['user_info']['user_password'],
            'user_password_check' => $_SESSION['user_info']['user_password_check'],
            'first_name'          => $_SESSION['user_info']['first_name'],
            'last_name'           => $_SESSION['user_info']['last_name'],
            'age'                 => $_SESSION['user_info']['age'],
            'tel_first'           => $_SESSION['user_info']['tel_first'],
            'tel_middle'          => $_SESSION['user_info']['tel_middle'],
            'tel_last'            => $_SESSION['user_info']['tel_last'],
            'user_mail'           => $_SESSION['user_info']['user_mail']
        ];

        // 「確認ページ」からのページバックフラグを初期化
        unset($_SESSION['btn_action_flag']['back_to_registrationPage']);
    }
?>
<head>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" type="text/css" href="/css/registration-member-style.css">
    <!-- インデックスするのを拒否 -->
    <meta name="robots" content="noindex">
</head>
<div class="registration-page-title wrapper">
    <h1>新規会員登録</h1>
</div>
<div class="wrapper">
    <!-- エラーメッセージ -->
    <?php if (isset($errMsg)): ?>
        <?php foreach($errMsg as $msg): ?>
            <p class="err-msg"><?php echo $msg; ?></p>
        <?php endforeach ?>
    <?php endif ?>

    <form class="registration-form" action="" method="post">
        <div class="registration-form__area wrapper disp-grid">
            <p>ユーザID</p>
            <!-- 英数字のみ許可 -->
            <input type="text" required placeholder="半角英数字のみ使用可能です。" name="user_id" value="<?php echo isset($userInfo['user_id']) ? escapeHtml($userInfo['user_id']) : ''; ?>">
            <!-- スペースのみは禁止 -->
            <p>名字</p>
            <input type="text" required maxlength="20" name="first_name" value="<?php echo isset($userInfo['first_name']) ? escapeHtml($userInfo['first_name']) : ''; ?>">
            <p>名前</p>
            <input type="text" required maxlength="20" name="last_name" value="<?php echo isset($userInfo['last_name']) ? escapeHtml($userInfo['last_name']) : ''; ?>">
            <p>年齢</p>
            <div class="registration-form__area__age disp-grid"><input type="number" required pattern="^[1-9][5-9]$" required name="age" value="<?php echo isset($userInfo['age']) ? escapeHtml($userInfo['age']) : ''; ?>"><span>　歳</span></div>
            <p>電話番号</p>
            <div class="registration-form__area__tel disp-grid">
                <div class="registration-form__area__tel__element disp-grid"><input type="number" required name="tel_first" value="<?php echo isset($userInfo['tel_first']) ? escapeHtml($userInfo['tel_first']) : ''; ?>"><span>-</span></div>
                <div class="registration-form__area__tel__element disp-grid"><input type="number" required name="tel_middle" value="<?php echo isset($userInfo['tel_middle']) ? escapeHtml($userInfo['tel_middle']) : ''; ?>"><span>-</span></div>
                <div class="registration-form__area__tel__element disp-grid"><input type="number" required name="tel_last" value="<?php echo isset($userInfo['tel_last']) ? escapeHtml($userInfo['tel_last']) : ''; ?>"></div>
            </div>
            <p>E-mail</p>
            <input type="text" maxlength="50" required placeholder="*****@co.jp" name="user_mail" value="<?php echo isset($userInfo['user_mail']) ? escapeHtml($userInfo['user_mail']) : ''; ?>">
            <p>パスワード</p>
            <input type="password" maxlength="50" pattern="^([a-zA-Z0-9]{8,})$" required placeholder="8文字以上の半角英数字" name="user_password" value="<?php echo isset($userInfo['user_password']) ? escapeHtml($userInfo['user_password']) : ''; ?>">
            <p>パスワード確認用</p>
            <input type="password" maxlength="50" pattern="^([a-zA-Z0-9]{8,})$" required placeholder="もう一度入力してください。" name="user_password_check" value="<?php echo isset($userInfo['user_password_check']) ? escapeHtml($userInfo['user_password_check']) : ''; ?>">
        </div>
        <button class="btn-color-next action-btn" type="submit" name="member_registration_btn" value="send_registration_info">確認</button>
    </form>
    <a class="return-to-login-page-link" href="./login.php">ログインページへ戻る</a>
</div>
