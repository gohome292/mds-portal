<?php echo $this->Html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->Html->charset();
echo $this->Html->meta('icon'); ?>
<title><?php echo strip_tags($page_title); ?></title>
<?php if (!DEBUG): ?>
<meta http-equiv="Refresh" content="<?php echo $pause; ?>;url=<?php echo $url; ?>"/>
<?php endif;
echo $this->element('css');
echo $this->Html->css('flash'); ?>
</head>
<body>
<div id="content">
<?php foreach ($records as $i => $record): ?>
<div><?php
if (!empty($record['url'])): 
    echo $this->Html->link(
        $record['message'],
        $record['url'],
        array('escape' => false)
    );
else:
    echo $record['message'];
endif; ?></div>
<?php if (!empty($records[($i+1)])) echo '<br />';
endforeach; ?>
</div><!-- #content -->
<?php echo $this->element('footer'); ?>
</body>
</html>
