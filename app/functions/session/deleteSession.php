<?php 
    declare(strict_types=1);

    /**
     * ログインユーザのすべてのセッションを削除する
     */
    function deleteSession() : void
    {
        if(ini_get('session.use_cookies')){
            $params = session_get_cookie_params();
            setcookie( session_name(), '', time() - 4200,
                $params['path'], $params['domain'], 
                $params['secure'], $params['httponly']     
            );
        } 
    }
?>