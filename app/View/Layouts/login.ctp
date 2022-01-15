<?php echo $this->Html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->Html->charset();
echo $this->Html->meta('icon'); ?>
<title><?php echo $title_for_layout; ?></title>
<?php echo $this->element('css');
echo $this->Html->css('default');
echo $this->element('javascript');
echo $this->Html->script('default'); ?>
</head>
<body>
<div id="content">
<?php echo $content_for_layout; ?>
</div><!-- #content -->
<?php echo $autoRead->run();
echo $this->element('footer'); ?>
</body>
</html>
