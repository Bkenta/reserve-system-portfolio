<?php
    declare (strict_types=1);

    /**
     * 確認用パスワードが正しいか判定
     *
     * @param String $userPassword ユーザパスワード
     * @param String $userPasswordCheck 確認用のユーザパスワード
     * @return boolean 等しくなければtrueを返す
     */
    function mismatchConfirmPass(String $userPassword, String $userPasswordCheck) : bool
    {
        return $userPassword !== $userPasswordCheck;
    }

    /**
     * ユーザID（情報）が登録済みかどうか
     *
     * @param array $userIdSet ユーザIDに基づく登録済みレコード
     * @return boolean 登録済みならtrueを返す
     */
    function ExitsUserId($userIdSet) : bool
    {
        return $userIdSet !== false;
    }

    /**
     * ユーザIDの存在チェック処理
     * 
     * @param String $inputUserId 登録しようとしているユーザID
     * @return boolean ユーザIDが未登録のものならばtrueを返す
     */
    function noExistId(String $inputUserId) : bool
    {
        try {
            $pdo = connect();
            $statment = $pdo->prepare('
                SELECT user_id 
                FROM users
                WHERE user_id = :inputUserId
            ');
            $statment->bindValue(':inputUserId', $inputUserId, PDO::PARAM_STR);
            $statment->execute();

            // データが取得されなかった場合はユーザIDが存在しないということのためtrueを返す
            if($statment->fetch(PDO::FETCH_ASSOC)){
                return true;
            }
        } catch(PDOException $e) {
            header('Location: ./err-page.php');
            exit;
        }
        // データが１件でもあればfalseを返す
        return false;
    }

    /**
     * ユーザIDの入力形式が正しいか判定
     *
     * @param String $inputUserId 登録しようとしているユーザID
     * @return boolean 8文字以上の半角英数字の組み合わせになっていたらtrueを返す
     */
     function validateUserId(String $inputUserId) : bool
    {
        return !preg_match('/^([a-zA-Z0-9]{8,})$/', $inputUserId);
    }
    
    /**
     * 入力人数に不正（デベロッパーツールによる人数の改ざん）が無いか判定
     * 1～8名  のみ許可
     * 
     * @param String $userNum 予約人数
     * @return boolean 1～8以外が入力されたらtrueを返す
     */
    function validateUserNum(String $userNum) : bool
    {
        return !preg_match('/[1-8]{1}$/', $userNum);
    }

    /**
     * 年齢の入力形式が正しいか判定
     * 15歳（～99歳）までは許可
     *
     * @param String $userAge 予約者の年齢
     * @return boolean 予約者の年齢が15歳以上99歳未満ならばtrueを返す
     */
    function validateAge(String $userAge) : bool
    {
        // 入力値がString型で入ってくるためint型にキャストしている
        return !preg_match('/^[1-9][0-9]$/', $userAge) || (int)$userAge < 15;
    }
    
    /**
     * 電話番号の入力形式が正しいか判定
     * 0から始まる4桁以下 4桁以下 4桁以下  のみ許可
     * 
     * ※入力項目は３つに分けてあるためハイフンの判定は不要
     * ※ユーザ登録ページで使用
     * 
     * @param String $userTelFirst  電話番号先頭
     * @param String $userTelMiddle 電話番号真ん中
     * @param String $userTelLast   電話番号最後
     * @return boolean 電話番号の形式になっていなければtrueを返す
     */
    function validateSplitTel(String $userTelFirst, String $userTelMiddle, String $userTelLast) : bool
    {
        return !(preg_match('/^0\d{1,4}$/', $userTelFirst) && preg_match('/^\d{1,4}$/', $userTelMiddle) && preg_match('/^\d{4}$/', $userTelLast));
    }
    
    /**
     * 電話番号の入力形式が正しいか判定
     * 0から始まる4桁以下-4桁以下-4桁以下  のみ許可
     * （例）080-1111-1111
     * 
     * ※入力項目をひとまとめにしてあるためハイフンの判定を含む
     * ※ログイン後以降の情報修正ページで使用
     *
     * @param String $userTel ハイフンを含む電話番号
     * @return boolean 電話番号の形式になっていなければtrueを返す 
     */
    function validateTel(String $userTel) : bool
    {
        return !preg_match('/^0\d{1,4}-\d{1,4}-\d{1,4}$/', $userTel);
    }

    /**
     * メールアドレスの入力形式が正しいか判定
     * *****@****.***の形式のみ許可
     * （例）example@gmail.com
     *
     * @param String $userMail ユーザメールアドレス
     * @return boolean メールアドレスの形式になっていなければtrueを返す 
     */
    function validateMail(String $userMail) : bool
    {
        return !preg_match('/^[a-zA-Z0-9_+-]+(.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/', $userMail);
    }

    /**
     * パスワードの入力形式が正しいか判定
     * 8文字以上の半角英数字
     * （例）1234abcd, 1a2b3cd45e
     *
     * @param String $userpassWord ユーザパスワード
     * @return boolean パスワードが半角英数字のみの組み合わせになっていなければtrueを返す
     */
    function validatePassWord(String $userpassWord) : bool
    {
        return !preg_match('/^([a-zA-Z0-9]{8,})$/', $userpassWord);
    }
?>