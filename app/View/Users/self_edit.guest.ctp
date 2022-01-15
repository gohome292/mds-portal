<?php echo $this->element('mds.nav');
echo $this->Html->script('users/self_edit.guest');
echo $this->Html->css('users/self_edit.guest'); ?>
<!-- MAIN --><div id="MAIN" role="main">
<?php echo $this->Form->create('User');
echo $this->Form->input('id'); ?>
<div class="pad">
    <dl>
        <dt>ログインID</dt>
        <dd><?php
        echo h($this->request->data['User']['username']);
        echo $this->Form->hidden('User.username');
        ?></dd>
    </dl>
    <dl>
        <dt>氏名</dt>
        <dd><?php
        echo h($this->request->data['User']['name']);
        echo $this->Form->hidden('User.name');
        ?></dd>
    </dl>
    <dl>
        <dt>メールアドレス</dt>
        <dd><?php
        echo h($this->request->data['User']['email']);
        echo $this->Form->hidden('User.email');
        ?></dd>
    </dl>
    <dl>
        <dt><label for="NewPW">新パスワード</label></dt>
        <dd><?php echo $this->Form->input(
            'password1',
            array(
                'type'     => 'password',
                'tabindex' => '1',
                'label'    => false,
            )
        ); ?>
        <div class="comment">セキュリティを考慮して、8文字以上を入力してください。</div></dd>
    </dl>
    <dl>
        <dt><label for="PWcfm">新パスワード確認</label></dt>
        <dd><?php echo $this->Form->input(
            'password2',
            array(
                'type'     => 'password',
                'tabindex' => '2',
                'label'    => false,
            )
        ); ?>
        <div class="comment">確認の為、再度同じパスワードを入力してください。</div></dd>
    </dl>
</div>
<?php echo $this->session->flash(); ?>
<div class="submit"><?php echo $this->Form->submit(
    'guest/btn/change_off.gif',
    array(
        'tabindex' => '3',
        'alt'      => '変更する',
   )
); ?></div>
<?php echo $this->Form->end(); ?>
<!-- /MAIN --></div>
