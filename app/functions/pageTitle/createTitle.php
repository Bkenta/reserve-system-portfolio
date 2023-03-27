<?php
    declare(strict_types=1);

    /**
     * 現在のファイル名（拡張子なし）を取得する
     * 
     * @return String 現在のファイル名（拡張子なし）
     */
    function getCurrentFilePath() : String
    {
        // ファイル名を拡張子なしで取得
        $associative_array = debug_backtrace(); // ['file']に現在のファイルのパスが入っている
        $thisFileName = basename($associative_array[1]['file'], '.php');

        return $thisFileName;
    }

    /**
     * 現在のページのタイトルに使用するclass名を返す
     * 
     * @param String $thisFileName 現在のファイル名（拡張子なし）
     * @return String 現在のページのタイトルに使用するclass名
     */
    function definitionPageTitleHtmlClassName(String $thisFileName) : String
    {
        return $thisFileName . '-wrapper';
    }
?>