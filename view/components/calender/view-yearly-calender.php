<!-- カレンダー本体 -->
<div class="calender">
    <form action="" method="POST">
        <input type="hidden" name="selectedYear" value="<?=$year?>">
        <input type="hidden" name="selectedMonth" value="<?=$month?>">
        <div class="calender-body">
            <div class="calender-body__header disp-grid grid-just-center grid-align-center">
                <p>日</p>
                <p>月</p>
                <p>火</p>
                <p>水</p>
                <p>木</p>
                <p>金</p>
                <p>土</p>
            </div>
            <div class="calender-body__daily-element disp-grid grid-just-center grid-align-center">
                <?php 
                /**
                 * カレンダーに日付を当て込む処理
                 * 7日出力するごとに改行
                 */ ?>
                <?php $date_set_cnt = 1; ?>
                <?php foreach($calenderDatesHasOption as $date): ?>
                    
                    <?php // 選択不可の日付はただの<p>タグで表示 ?>
                    <?php if($date['dispCalenderFlag']): ?>
                    <p>
                        <button type="submit" name="user_select_date" value="<?php echo $date['date']; ?>"><?php echo $date['date']; ?></button>
                    </p>
                    <?php else: ?>
                        <p style="color:#808080; font-size: 1.3rem">
                            <?php echo $date['date']; ?>
                    </p>
                    <?php endif ?>
                    
                <?php $date_set_cnt++; ?>
                <?php endforeach ?>
            </div>
        </div>
    </form>
</div>