<?php
    declare (strict_types=1);

    /**
     * 出力するHTMLにエスケープ処理を実行
     *
     * @param String|null $value HTML上で出力したい文字列
     * @return String エスケープしたHTMLで出力する文字列
     */
    function escapeHtml(?String $value) : String
    {
        return htmlspecialchars(strval($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // TODO:エスケープ処理の実装
    // function escapeLike (?string $value)
    // {
    //     return preg_replace ('/[%_#]/u', '#${1}', $value);
    // }
?>