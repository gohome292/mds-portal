<?php echo $this->Html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->Html->charset();
echo $this->Html->meta('icon'); ?>
<title><?php echo strip_tags($page_title); ?></title>
<meta http-equiv="Refresh" content="<?php echo $pause; ?>;url=<?php echo $url; ?>"/>
<?php echo $this->element('css');
echo $this->Html->css('flash'); ?>
</head>
<body>
<div id="content">
<?php echo $message; ?>
</div><!-- #content -->
<?php echo $this->element('footer'); ?>
</body>
</html>
