<?php if (Configure::read('App.screen.compact')):
echo $this->Html->css('elements/compact.edit');
endif;
echo $this->Html->script('elements/edit');

echo $this->Form->create('User');
echo $this->Form->input('id'); ?>
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['username']; ?></th>
        <td><?php
        echo h($this->request->data['User']['username']);
        echo $this->Form->hidden('User.username');
        ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['name']; ?></th>
        <td><?php
        echo h($this->request->data['User']['name']);
        echo $this->Form->hidden('User.name');
        ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['password']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('password1', array('type' => 'password', 'label' => false, 'div' => false )); ?>
        <div class="comment">セキュリティを考慮して、8文字以上を入力してください。</div></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['password']; ?>確認<span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('password2', array('type' => 'password', 'label' => false, 'div' => false)); ?>
        <div class="comment">確認の為、再度同じパスワードを入力してください。</div></td>
    </tr>
<?php echo $this->element('edit_common_area'); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
