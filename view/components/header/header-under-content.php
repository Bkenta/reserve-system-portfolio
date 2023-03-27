<?php
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 4) . '/app/functions/pageTitle/createTitle.php');
        require_once(dirname(__FILE__, 4) . '/app/constants/titleData.php');        // 各ページタイトル用データの読み込み

    } catch(Exception $e) {
        escapePageToErrPage();
    }

    // ファイル名を拡張子なしで取得
    $thisFileName = getCurrentFilePath();

    // class名の定義
    // 各ページのタイトル部分(HTML)で使用している
    $className = definitionPageTitleHtmlClassName($thisFileName);

    // ユーザ名
    $userDisplayName = $_SESSION['user_info']['first_name'] . $_SESSION['user_info']['last_name'];
?>
<div class="header-under-wrapper disp-grid grid-just-left">
    <!-- タイトル -->
    <div class="header-under-wrapper__page-title">
        <h1><?php echo $pagetitleArray[$thisFileName]; ?></h1>
    </div>
    <!-- ユーザ情報 -->
    <div class="header-under-wrapper__user-area">
        <p class=""><?php echo 'ようこそ ' . escapeHtml($userDisplayName) . ' 様'; ?></p>
        <!-- ログアウトボタン -->
        <form action="" method="post">
            <button class="btn-color-logout action-btn" type="submit" name="logout-btn" onclick="return confirm('ログアウトしますか？ \n※入力中の内容はすべて削除されます。')">ログアウト</button>
        </form>
    </div>
</div>