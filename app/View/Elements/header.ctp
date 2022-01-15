<?php echo $this->Html->css('elements/header');
echo $this->Html->script('elements/header'); ?>


<div class="header horizon">
<ul>
<?php if (!empty($mainmenus)): ?>
<li class="submit"><input type="button" value="メニュー" id="mainmenu_button" /></li>
<?php else: ?>
<li class="submit"><input type="button" value="閉じる" class="close" /></li>
<?php endif; ?>
<li class="username text">【<?php echo h($this->session->read('Auth.User.name')); ?>】</li>
<li class="text"><?php echo h($breadcrumbs); ?></li>
</ul>
</div><!-- .header -->
<div class="hr"><hr /></div>


<?php if (!empty($mainmenus)) $this->Tree->run($mainmenus); ?>
