<?php if (Configure::read('App.screen.compact')):
echo $this->Html->css('elements/compact.index');
endif; ?>
<div class="menu horizon">
<ul>
<?php echo $this->menu->add(); ?>
</ul>
</div><!-- .menu -->
