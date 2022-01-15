<?php echo $this->html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->html->charset();
echo $this->html->meta('icon'); ?>
<title><?php echo strip_tags($title_for_layout); ?></title>
<?php if (!DEBUG): ?>
<meta http-equiv="Refresh" content="<?php echo $pause; ?>;url=<?php echo $url; ?>"/>
<?php endif;
echo $this->element('css');
echo $this->html->css('flash'); ?>
</head>
<body>
<div id="content">
<a href="<?php echo $url; ?>"><?php echo $message; ?></a>
</div><!-- #content -->
<?php echo $this->Element('footer'); ?>
</body>
</html>
