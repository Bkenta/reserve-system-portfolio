<?php 
    declare(strict_types=1);

    /**
     * 「ユーザが選択可能なラジオボタンID」と「DBから取得した日付別のレコードID」を紐づけする
     * ◆ $infoDisplayRecordNum                       ：「予約データごとのラジオボタン」に割り振る固有ID……これによりどの日付データが選択されたのかを判別する
     * ◆ $reservedInfoDBIDs[ $infoDisplayRecordNum ] ：「DB上における同日の予約情報ID」の（複数の時間帯で予約がある場合は複数レコードが対象）配列
     * ◆ $reservedInfos['record_ids']                ：「同日の予約情報レコードID」をカンマ区切りでひとまとめにしたデータ
     * 例）2日分の予約をしている人の場合
     * 【[1]件目】2023年1月20日 12～14時, 14～16時, 16～18時 → $reservedInfoDBIDs[1] = {17, 18, 19};  // DB上で17～19のレコードIDを持つということ
     * 【[2]件目】2023年1月24日 10～12時, 12～14時           → $reservedInfoDBIDs[2] = {24, 25};      // DB上で24～25のレコードIDを持つということ
     * 
     * ラジオボタンで選択された予約データの$infoDisplayRecordNumが「更新ページ」へ送信され、
     * $infoDisplayRecordNumに改ざんが無いことをチェックしてから「更新ページ」をブラウザに表示する。
     * 
     * @param array $reservedInfoSets 予約情報データセット
     * @return array 「ユーザが選択可能なラジオボタンID」と「DBから取得した日付別のレコードID」を紐づけした配列
     */
    function linkSelectedRadioIdAndReservedInfosAboutDataOnDB(array $reservedInfoSets) : array
    {
        // 「ブラウザ上での日付別データ」に割り振る固有ID
        $infoDisplayRecordNum = 0;
        
        foreach($reservedInfoSets as $reservedInfos){
            $reservedInfoDBIDs[++$infoDisplayRecordNum] = $reservedInfos['record_ids'];
        }

        return $reservedInfoDBIDs;
    }
?>