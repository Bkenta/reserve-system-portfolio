<?php 
    /**
     *  ログイン画面
     *  ID・パスワードによる検証処理を行う 
     */
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 3) . '/app/functions/db/dbConnection.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/validate/inputInfovalidate.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/escapePage/escapeToOtherPage.php');

    } catch(Exception $e) {
        escapePageToErrPageBeforeLogin();
    }

    // ログイン済みならhomuMenu.phpへ遷移
    if(isset($_SESSION['user_info']['user_id'])) escapePageToHomeMenu();

    // user_idによるレコード取得
    if(isset($_POST['login_btn'])){
        
        session_start();

        // 入力されたID・パスワード
        $userInputId   = $_POST['user_id'];
        $userInputPass = $_POST['user_pass'];
        $userPassHash  = '';

        // user_idが登録済みか確認
        try {
            $pdo = connect();
            $pdo->beginTransaction();
            $statment = $pdo->prepare('
                SELECT * 
                FROM 
                    users 
                WHERE 
                    user_id = :user_id
            ');
            $statment->bindValue(':user_id', $userInputId, PDO::PARAM_STR);
            $statment->execute();

            $userIdSet = $statment->fetch(PDO::FETCH_ASSOC);

            $pdo->commit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            escapePageToErrPageBeforeLogin();
        }
        
        // ハッシュパスワードの取得
        if(ExitsUserId($userIdSet)) $userPassHash = $userIdSet['user_password'];
        
        // パスワード検証
        if(password_verify ($userInputPass, $userPassHash)){
            // セッションユーザ情報を格納
            $_SESSION['user_info'] = array (
                'users_table_id'   => $userIdSet['id'],
                'user_id'          => $userIdSet['user_id'],
                'user_password'    => $userIdSet['user_password'],
                'first_name'       => $userIdSet['first_name'],
                'last_name'        => $userIdSet['last_name'], 
                'age'              => $userIdSet['age'],
                'telphone_number'  => $userIdSet['telphone_number'],
                'user_mail'        => $userIdSet['mail_address']
            );
            escapePageToHomeMenu();
        }
        $errMsg['misMatchIdPass'] = '入力されたIDまたはパスワードに誤りがあります。';
    }
?>
<head>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" type="text/css" href="/css/login-style.css">
    <!-- インデックスするのを拒否 -->
    <meta name="robots" content="noindex">
</head>
<!-- タイトル -->
<div class="wrapper login-title">
    <h1>ログイン画面</h1>
</div>

<!-- ログインフォームエリア -->
<div class="wrapper loginFormArea">
    <!-- エラーメッセージ -->
    <?php if (isset($errMsg)): ?>
        <?php foreach($errMsg as $msg): ?>
            <p class="err-msg"><?php echo $msg; ?></p>
        <?php endforeach ?>
    <?php endif ?>
    
    <!-- 入力フォーム -->
    <div class="loginFormArea__inputForm">
        <form action="" method="POST">
            <div class="loginFormArea__inputForm__wrapper disp-grid">
                <p>ID：</p><input type="text" name="user_id" value="<?php echo isset($userInputId) ? $userInputId : ""; ?>">
                <p>パスワード：</p><input type="password" name="user_pass">
            </div>
            <button class="btn-color-confirm action-btn" type="submit" name="login_btn">ログイン</button>
        </form>
    </div>
</div>

<!-- リンク -->
<div class="wrapper">
    <p><a href="./member-registration.php" name="registration_entry_btn">新規登録はこちら</a></p>
</div>


