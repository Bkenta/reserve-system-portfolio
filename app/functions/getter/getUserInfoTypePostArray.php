<?php 
    declare(strict_types=1);

    /**
     * 新規登録会員のPOST入力情報を連想配列で取得する関数 function
     *
     * @return '新規会員のPOST入力情報の連想配列'
     */
    function getUserInfoTypePostArray() : array
    {
        $userInfo = [
            'user_id'             => $_POST['user_id'],
            'user_password'       => $_POST['user_password'],
            'user_password_check' => $_POST['user_password_check'],
            'first_name'          => $_POST['first_name'],
            'last_name'           => $_POST['last_name'],
            'age'                 => $_POST['age'],
            'tel_first'           => $_POST['tel_first'],
            'tel_middle'          => $_POST['tel_middle'],
            'tel_last'            => $_POST['tel_last'],
            'user_mail'           => $_POST['user_mail']
        ];
        return $userInfo;
    }
?>