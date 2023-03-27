<?php 
    declare(strict_types=1);

     /**
      * 予約可否表示情報の初期配列を生成
      * @param String $reserveTimeRangeList 予約時間枠リスト
      * @return array 予約可否表示情報の初期配列
      */
      function initBrowsConditionArray(array $reserveTimeRangeList) : array
      {
          // 初期表示情報
          $browsInitCondition = '〇';
          $reserveBrowsConditionArray = array(
              $reserveTimeRangeList[0] => $browsInitCondition,
              $reserveTimeRangeList[1] => $browsInitCondition,
              $reserveTimeRangeList[2] => $browsInitCondition,
              $reserveTimeRangeList[3] => $browsInitCondition,
              $reserveTimeRangeList[4] => $browsInitCondition
          );
          return $reserveBrowsConditionArray;
      }

       /**
        * 予約可否表示情報の初期配列を生成
        * @param String $reserveTimeRangeList     予約時間枠リスト
        * @return array 予約時間チェックボックスstyleの初期配列
        */
      function initInputerVisibleStyleArray(array $reserveTimeRangeList) : array
      {
          $inputerVisibleInitStyle = 'visible';
          $reserveInputerVisibleStyleArray = array(
              $reserveTimeRangeList[0] => $inputerVisibleInitStyle,
              $reserveTimeRangeList[1] => $inputerVisibleInitStyle,
              $reserveTimeRangeList[2] => $inputerVisibleInitStyle,
              $reserveTimeRangeList[3] => $inputerVisibleInitStyle,
              $reserveTimeRangeList[4] => $inputerVisibleInitStyle
          );
          return $reserveInputerVisibleStyleArray;
      }
?>