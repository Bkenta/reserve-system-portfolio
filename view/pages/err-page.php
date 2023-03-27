<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 2) . '/components/header/header.php');

    } catch(Exception $e) {
        escapePageToErrPage();
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
<div class="wrapper">
    <p class="lead-text">予期しないエラーが発生しました。</p>
    <p>大変恐れ入りますが下記より改めてお試しください。</p>
    <a href="<?php echo './homuMenu.php'; ?>">トップページへ</a>
</div>