<?php
echo $this->Html->script('users/request_reset');
echo $this->Form->create('User');
?>
<div class="box">
    <h2>パスワード初期化申請</h2>
    <p>ログインIDとメールアドレスを入力し、初期化申請ボタンを押してください。<br />
    申請が受け付けられましたら、ご指定のメールアドレスにメールが送信されます。</p>
    <div class="pad">
        <dl>
            <dt><label for="UserID">ログインID</label></dt>
            <dd><?php echo $this->Form->input("username", array('label' => false, 'div' => false)); ?></dd>
        </dl>
        <dl>
            <dt><label for="UserMail">メールアドレス</label></dt>
            <dd><?php echo $this->Form->input("email", array('label' => false, 'div' => false)); ?></dd>
        </dl>
        <dl>
            <dt><label for="UserMail2">メールアドレス(確認)</label></dt>
            <dd><?php echo $this->Form->input("email2", array("maxlength" => "80", 'label' => false, 'div' => false)); ?></dd>
        </dl>
    </div>
</div>
<div class="alert">
    <p><?php
    $flash = $this->session->flash();
    if (!empty($flash)):
        echo $flash;
    else:
        echo '&nbsp;';
    endif;
    ?></p>
</div>
<div class="submit">
    <input type="image" alt="初期化申請" src="<?php echo $this->base."/img/guest/btn/reset_off.gif"; ?>" />
</div>
<?php echo $this->Form->end(); ?>
