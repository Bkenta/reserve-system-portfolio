<?php
    declare(strict_types=1);

    try {
        require_once dirname(__FILE__, 2) . '/const.php';
        require_once(dirname(__FILE__, 2) . '/escapePage/escapeToOtherPage.php');

    } catch(Exception $e) {
        escapePageToErrPage();
    }

    /**
     * DBに接続
     * エラーモードの設定
     * エミュレーションをオフに設定
     */
    function connect()
    {
        try {
            $pdo = new PDO( 'mysql:host=' . DB_HOSTNAME . '; dbname=' . DB_NAME . ';charset=utf8mb4;', DB_ADMINNAME, DB_PATH );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch(PDOException $e) {
            escapePageToErrPageBeforeLogin();
        }
        return $pdo;
    }
?>