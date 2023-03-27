<!-- 年月切り替えボタン -->

<div class="prev-next-btn-area disp-grid grid-just-center grid-align-center">
    <!-- 前月 -->
    <div class="prev-change-btn disp-grid grid-just-center grid-align-center" <?php if(!$isDispPrevMonthBtn) echo 'style="display:none"'; ?> >
        <form action="" method="POST">
            <div class="prev-change-btn__in-form disp-grid grid-just-center grid-align-center">
                <span>Last Month</span>
                <button type="submit" id="prev-btn">◀
                    <input type="hidden" name="prevMonth" value="<?php echo $month - 1; ?>">
                    <input type="hidden" name="prevYear" value="<?php echo $year; ?>">
                </button>
            </div>
        </form>
    </div>
    <!-- 翌月 -->
    <div class="next-change-btn">
        <form action="" method="POST">
            <div class="next-change-btn__in-form disp-grid grid-just-center grid-align-center">
                <button type="submit" id="next-btn">▶
                    <input type="hidden" name="nextMonth" value="<?php echo $month + 1; ?>">
                    <input type="hidden" name="nextYear" value="<?php echo $year; ?>">
                </button>
                <span>Next Month</span>
            </div>
        </form>
    </div>
</div>