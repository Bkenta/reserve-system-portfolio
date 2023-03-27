<?php
    /**
     * カレンダーの日付を生成
     *
     * @param integer $month 表対象の月
     * @param integer $year 表示対象の年
     * @return array 表示対象の年月の日付配列
     */
    function createYearlyCalender(int $month, int $year) : array
    {
        $calenderDates = array();                                                //日付の配列
        $lastDate = date('j', mktime(0, 0, 0, (int)$month+1, 0, (int)$year));    //日付生成ループ制御用に月末日を取得
        for($i=1; $i<=$lastDate; $i++){
            $calenderDates[] = $i;
        }
        // カレンダーのついたちの位置をオフセット
        $calenderDatesIsOffset = offsetCalenderFirstDay((int)$month, (int)$year, (array)$calenderDates);

        return $calenderDatesIsOffset;
    }

    /**
     * カレンダーのついたちの位置をオフセット
     *
     * @param integer $month 表対象の月
     * @param integer $year  表示対象の年
     * @param array $calenderDates 表示対象の年月の日付配列
     * @return array ついたちの開始位置をオフセットしたカレンダーの日付配列
     */
    function offsetCalenderFirstDay(int $month, int $year, array $calenderDates) : array
    {
        $dayOfWeekNum = date('w', mktime(0, 0, 0, (int)$month, 1, (int)$year));  // 曜日（番号）の取得
        for($j=0; $j<$dayOfWeekNum; $j++){                                       // 曜日（番号）に応じて日付の空データを生成
            array_unshift($calenderDates, '');
        }
        return $calenderDates;
    }
    
    /**
     * 前月の「年月」を返す
     *
     * @param integer $prevMonth 前月
     * @param integer $prevYear 前月の年
     * @return array  表示する「前月」と「前月の年」を格納した配列
     */
    function setPrevMonthAndYear(int $prevMonth, int $prevYear) : array
    {
        if($prevMonth < 1){
            // 前月が0月となるとき
            $month = 12;
            $year  = $prevYear - 1;
        } else {
            $month = $prevMonth;
            $year  = $prevYear;
        }

        $pairMonthAndYear = array(
            'month' => $month,
            'year'  => $year
        );
        return $pairMonthAndYear;
    }

    /**
     * 翌月の「年月」を返す
     *
     * @param integer $nextMonth 翌月
     * @param integer $nextYear 翌月の年
     * @return array  表示する「翌月」と「翌月の年」を格納した配列
     */
    function setNextMonthAndYear(int $nextMonth, int $nextYear) : array
    {
        if($nextMonth > 12){
            // 翌月が13月となるとき
            $month = 1;
            $year  = $nextYear + 1;
        } else {
            $month = $nextMonth;
            $year  = $nextYear;
        }

        $pairMonthAndYear = array(
            'month' => $month,
            'year'  => $year
        );
        return $pairMonthAndYear;
    }
?>