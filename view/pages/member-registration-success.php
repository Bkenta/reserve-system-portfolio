<?php
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 3). '/app/functions/session/deleteSession.php');
        require_once(dirname(__FILE__, 3). '/app/functions/escapePage/escapeToOtherPage.php');
        
    } catch (Exception $e) {
        escapePageToErrPageBeforeLogin();
    }

    session_start();

    // URL入力による直アクセスは禁止
    if(!isset($_SESSION['user_info'])) escapePageToLogin();
?>
<head>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" type="text/css" href="/css/registration-member-style.css">
    <!-- インデックスするのを拒否 -->
    <meta name="robots" content="noindex">
</head>
<div class="registrationsuccess-page-title wrapper">
    <h1>会員登録が完了しました。</h1>
</div>

<div class="wrapper">
    <a href="./login.php">ログインページへ</a>

    <?php $_SESSION = []; ?>
    <?php deleteSession(); ?>
    <?php session_destroy(); ?>
</div>