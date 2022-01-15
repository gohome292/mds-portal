<?php if (Configure::read('App.screen.compact')):
echo $this->Html->css('elements/compact.edit');
endif; ?>
<div class="menu horizon">
<ul>
<?php echo $this->menu->back(); ?>
</ul>
</div><!-- .menu -->
