<!-- ユーザ情報 -->
<div class="user-option-area disp-grid gird-just-center grid-align-center">
    <div class=""><?php echo 'ようこそ ' . escapeHtml($userDisplayName) . ' 様'; ?></div>
    <!-- ログアウトボタン -->
    <form action="" method="post">
        <button type="submit" name="logout-btn" onclick="return confirm('ログアウトしますか？ \n※入力中の内容はすべて削除されます。')">ログアウト</button>
    </form>
</div>
