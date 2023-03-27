<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header.php');
        require_once(dirname(__FILE__, 3) . '/app/functions/escapePage/escapeToOtherPage.php');    
    } catch (Exception $e) {
        escapePageToErrPage();
    }

    // URLによる直接アクセスを拒否
    if(!(isset($_SESSION['reserve_info_data']['user_num']) && 
         isset($_SESSION['reserve_info_data']['contact_phone_number']) && 
         isset($_SESSION['reserve_info_data']['contact_mailAddress']))) escapePageToHomeMenu();
?>
<head>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<?php 
    // タイトル・ログアウトボタンの読み込み
    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header-under-content.php');

    } catch(Exception $e) {
        escapePageToErrPage();
    }
?>
<div class="wrapper">
    <h1 class="success-msg-h1">予約が完了しました。</h1>
    <p class="success-msg">当日のご来店お待ちしています。</p>

    <a href="./reserve-condition-page.php">予約状況確認</a>
</div>

<?php 
    // セッションを初期化する
    unset($_SESSION['reserve_info_data']);
?>