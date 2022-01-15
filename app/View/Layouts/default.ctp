<?php echo $this->html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->html->charset();
echo $this->html->meta('icon'); ?>
<title><?php echo $title_for_layout; ?></title>
<?php echo $this->element('css');
echo $this->html->css('default');
echo $this->element('javascript');
echo $this->Html->script('default'); ?>
</head>
<body>
<div id="content">
<?php echo $this->element('mds.header');
echo $this->element('header');
echo $content_for_layout; ?>
</div><!-- #content -->
<?php echo $this->autoRead->run();
echo $this->element('footer'); ?>
</body>
</html>
