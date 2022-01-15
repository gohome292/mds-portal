<style><!--
div#forgot_passwd {
    width: 620px;
    margin: auto;
    margin-top: 2px;
    text-align: right;
}
--></style>
<?php echo $this->Html->script('users/login.guest'); ?>
<!-- MAIN --><div id="MAIN" role="main">
<div id="PROMO"><?php echo $this->Html->image(
            'guest/main/login.jpg',
            array(
                'width'  => '890',
                'align'  => 'center',
                'alt'    => 'Managed Document Services&trade; - MPS and Beyond',
            )
        ); ?></div>
<?php if (Configure::read('Mds.login') || $ClientIP == Configure::read('Mds.ClientIP')): ?>
<?php echo $this->Form->create('User', array('url' => 'login')); ?>
<div class="pad">
    <dl>
        <dt><label for="UserID">ログインID</label></dt>
        <dd><?php echo $this->Form->input(
            'username',
            array(
                'label'    => false,
                'tabindex' => '1',
                'style'    => 'ime-mode: disabled;',
            )
        ); ?></dd>
    </dl>
    <dl>
        <dt><label for="UserPW">パスワード</label></dt>
        <dd><?php echo $this->Form->input(
            'password',
            array(
                'label'    => false,
                'tabindex' => '2',
            )
        ); ?></dd>
    </dl>
</div>
<!--<div id="forgot_passwd">
    <?php echo $this->Html->link("パスワードをお忘れの方はこちら", "/users/request_reset/"); ?>
</div>-->
<div class="alert">
    <p><?php
    if (!empty($this->request->data) || !Configure::read('App.loginAutoRedirect')):
        echo $this->session->flash('auth');
    else:
        echo '&nbsp;';
    endif;
    ?></p>
</div>
<div class="submit"><?php echo $this->Form->submit(
    'guest/btn/login_off.gif',
    array(
        'tabindex' => '3',
        'alt'      => 'ログイン',
    )
); ?></div>
<?php else: ?>
<br /><br /><br /><br />
<?php endif; ?>
<?php echo $this->Form->end();
App::import('Vendor', 'Iggy.nl2p');
App::import('Vendor', 'Iggy._h'); ?>
<div class="half clear">
    <div class="column">
        <?php if (!empty($system_messages['SystemMessage']['left'])): ?>
        <h2><?php echo $system_messages['SystemMessage']['left_subject']; ?></h2>
        <div class="box"><?php echo $this->richText->createLink(nl2p(_h($system_messages['SystemMessage']['left_text'])), array('target' => '_blank', 'class' => 'exit')); ?></div>
        <?php endif; ?>
    </div>
    <div class="column">
        <?php if (!empty($system_messages['SystemMessage']['right'])): ?>
        <h2><?php echo $system_messages['SystemMessage']['right_subject']; ?></h2>
        <div class="box"><?php echo $this->richText->createLink(nl2p(_h($system_messages['SystemMessage']['right_text'])), array('target' => '_blank', 'class' => 'exit')); ?></div>
        <?php endif; ?>
    </div>
</div>
<!-- /MAIN --></div>
