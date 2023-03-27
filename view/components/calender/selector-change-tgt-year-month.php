    <!-- 指定年月セレクター -->
    <div class="changer-month-year">
        <form action="" method="POST">
            <select name="tgtYear_tgtMonth">
                <option value="default"></option>
                <!-- 年月リストの取り出し -->
                <?php foreach ( $tgt_month_year_array as $tgt_month_year ): ?>
                    <?php (string)$tgt_year_str = $tgt_month_year[0]; // 年 ?>
                    <?php (string)$tgt_month_str = $tgt_month_year[1]; // 月 ?>
                    <option value="<?php echo $tgt_year_str . '&' . $tgt_month_str ?>"><?php echo $tgt_year_str . "年 " . $tgt_month_str . "月"; ?></option>
                <?php endforeach ?>
            </select>
            <button type="submit">表示</button>
        </form>
    </div>