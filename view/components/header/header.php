<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 4) . '/app/functions/logout/logout.php');
        require_once(dirname(__FILE__, 4) . '/app/functions/validate/escape.php');
        require_once(dirname(__FILE__, 4) . '/app/functions/escapePage/escapeToOtherPage.php');

    } catch(Exception $e) {
        escapePageToErrPage();
    }
    // ログイン時にユーザ情報をセッションに保持するところまでは完了 
    session_start();

    // ログイン状態で無ければログインページへ遷移
    if(!isset($_SESSION['user_info']['user_id'])) escapePageToErrPageBeforeLogin();

    // ログアウトボタンが押下されたらセッションを完全削除してログアウト実行
    if(isset($_POST['logout-btn'])) deleteSessionForLogout();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="カラオケ予約システムのポートフォリオ作品です。【言語】PHP【フロント】HTML・CSS">

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" type="text/css" href="/css/header-style.css">
    <link rel="stylesheet" type="text/css" href="/css/homeMenu-style.css">
    <link rel="stylesheet" type="text/css" href="/css/calender-style.css">
    <link rel="stylesheet" type="text/css" href="/css/daily-time-range-style.css">
    <link rel="stylesheet" type="text/css" href="/css/reserve-info-input-style.css">
    <link rel="stylesheet" type="text/css" href="/css/reserve-confirm-style.css">
    <link rel="stylesheet" type="text/css" href="/css/reserve-condition-style.css">
    <link rel="stylesheet" type="text/css" href="/css/reserve-update-style.css">
    <link rel="stylesheet" type="text/css" href="/css/reserve-update-success-style.css">
    <link rel="stylesheet" type="text/css" href="/css/reserve-delete-style.css">
    <link rel="stylesheet" type="text/css" href="/css/reserve-delete-success-style.css">
	
    <!-- インデックスするのを拒否 -->
    <meta name="robots" content="noindex">

    <title>Karaoke予約サービス</title>
</head>
<body>
    <header>
        <div class="header-section header-wrapper disp-grid gird-just-center">
            <!-- タイトル -->
            <div class="header-section__title">
                <a class="disp-grid" href="./homeMenu.php">
                    <img class="opasity-5 img-size-middle" src="../assets/img/mic-icon.png" alt="マイクアイコン">
                    <p>予約サービス</p>
                </a>
            </div>
            <!-- 右上ボタンエリア -->
            <div class="header-section__btn-area disp-grid grid-just-right grid-align-center">
                <div class="header-section__btn-area__reserve-btn disp-grid grid-just-center grid-align-center btn-small btn-hover-opacity">
                    <a href="./reserve-yearMonthCalender-page.php"></a>
                    <span class="text-color-white">予約</span>
                    <img class="img-size-60" src="/assets/img/mic-icon.png" alt="マイクアイコン">
                </div>
                <div class="header-section__btn-area__reserve-condition-btn disp-grid grid-just-center grid-align-center btn-small btn-hover-opacity">
                    <a href="./reserve-condition-page.php"></a>
                    <span class="text-color-white">予約状況の確認</span>
                    <img class="img-size-50" src="/assets/img/calender-icon.png" alt="カレンダーアイコン">
                </div>
            </div>
        </div>
    </header>