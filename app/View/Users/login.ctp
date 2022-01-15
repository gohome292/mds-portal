<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login'); ?>
<div class="login">
<div class="frame">
<?php //echo $this->Html->image('logo_top.gif') ?><br /><br /><br /><br /><br /><br /><br />
<table>
    <tr>
        <td><?php //echo $this->Html->image('logo_left.gif') ?></td>
        <td>
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['username']; ?></th>
        <td><?php echo $this->Form->input('username'); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['password']; ?></th>
        <td><?php echo $this->Form->input('password'); ?></td>
    </tr>
    <tr>
        <td colspan="2" class="right"><?php echo $this->Form->submit('ログイン'); ?></td>
    </tr>
</table>
</div><!-- .list -->
        </td>
    </tr>
</table>
<div class="failure center"><?php $this->session->flash('auth'); ?></div>
</div><!-- .login -->
<?php echo $this->Form->end(); ?>
