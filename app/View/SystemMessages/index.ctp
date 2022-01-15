<?php if (Configure::read('App.screen.compact')):
echo $this->Html->css('elements/compact.edit');
endif;
echo $this->Html->script('elements/edit');
echo $this->Form->create('SystemMessage'); ?>
<div class="list">
<table>
    <tr>
        <td><?php echo $this->Form->checkbox('left'); ?>表示する<br />
        <?php echo $this->Form->text('left_subject'); ?><br />
        <?php echo $this->Form->textarea('left_text'); ?></td>
        <td><?php echo $this->Form->checkbox('right'); ?>表示する<br />
        <?php echo $this->Form->text('right_subject'); ?><br />
        <?php echo $this->Form->textarea('right_text'); ?></td>
    </tr>
    <tr>
        <td colspan="2" class="right"><?php echo $this->Form->submit('保　存', array('class' => 'save')); ?></td>
    </tr>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
