<?php 
    declare(strict_types=1);

    try {
        require_once(dirname(__FILE__, 2) . '/session/deleteSession.php');

    } catch(Exception $e) {
        escapePageToErrPage();
    }
    
    /**
     * セッション変数を削除し、ログイン画面へ遷移する＝ログアウト処理の実行
     */
    function deleteSessionForLogout() : void
    {
        $_SESSION = [];
        deleteSession();
        session_destroy();

        header('Location: ./login.php');
        return;
    }
?>