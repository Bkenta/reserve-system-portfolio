<?php 
    declare(strict_types=1);

    /**
     * （未ログイン状態）エラーページへ遷移させる
     */
    function escapePageToErrPageBeforeLogin() : void 
    {
        header('Location: ./err-page-before-login.php');
        exit;
    }

    /**
     * （ログイン完了状態）エラーページへ遷移させる
     */
    function escapePageToErrPage() : void 
    {
        header('Location: ./err-page.php');
        exit;
    }

    /**
     * ログインページへ遷移させる
     */
    function escapePageToLogin() : void 
    {
        header('Location: ./login.php');
        exit;
    }

    /**
     * トップページへ遷移させる
     */
    function escapePageToHomeMenu() : void 
    {
        header('Location: ./homeMenu.php');
        exit;
    }
    
    /**
     * 新規会員登録内容入力ページへ遷移させる
     */
    function escapePageToMemberRegistration() : void 
    {
        header('Location: ./member-registration.php');
        exit;
    }
    
    /**
     * 新規会員登録内容確認ページへ遷移させる
     */
    function escapePageToMemberRegistrationConfirm() : void 
    {
        header('Location: ./member-registration-confirm.php');
        exit;
    }
    
    /**
     * 新規会員登録成功ページへ遷移させる
     */
    function escapePageToMemberRegistrationSuccess() : void 
    {
        header('Location: ./member-registration-success.php');
        exit;
    }
    
    /**
     * カレンダーページへ遷移させる
     */
    function escapePageToReserveYearMonthCalenderPage() : void 
    {
        header('Location: ./reserve-yearMonthCalender-page.php');
        exit;
    }
    
    /**
     * 時間選択ページへ遷移させる
     */
    function escapePageToReserveTimeRangeCalenderPage() : void 
    {
        header('Location: ./reserve-timeRangeCalender-page.php');
        exit;
    }
    
    /**
     * 予約情報入力ページへ遷移させる
     */
    function escapePageToReserveInputInfoPage() : void 
    {
        header('Location: ./reserve-inputInfo-page.php');
        exit;
    }
    
    /**
     * 予約内容確認ページへ遷移させる
     */
    function escapePageToReserveConfirmPage() : void 
    {
        header('Location: ./reserve-confirm-page.php');
        exit;
    }

    /**
     * 予約成功ページへ遷移させる
     */
    function escapePageToReserveSuccessPage() : void 
    {
        header('Location: ./reserve-success-page.php');
        exit;
    }
    
    /**
     * 予約状況確認ページへ遷移させる
     */
    function escapePageToReserveConditionPage() : void 
    {
        header('Location: ./reserve-condition-page.php');
        exit;
    }

    /**
     * 予約内容更新ページへ遷移させる
     */ 
    function escapePageToReserveUpdatePage() : void 
    {
        header('Location: ./reserve-update-page.php');
        exit;
    }

    /**
     * 予約内容更新成功ページへ遷移させる
     */ 
    function escapePageToReserveUpdateSuccessPage() : void 
    {
        header('Location: ./reserve-update-success-page.php');
        exit;
    }
    
    /**
     * 予約内容キャンセルページへ遷移させる
     */
    function escapePageToReserveDeletePage() : void 
    {
        header('Location: ./reserve-delete-page.php');
        exit;
    }
    
    /**
     * 予約内容キャンセル成功ページへ遷移させる
     */
    function escapePageToReserveDeleteSuccessPage() : void 
    {
        header('Location: ./reserve-delete-success-page.php');
        exit;
    }

?>