<?php
echo $this->Html->script('vendors/jquery');
echo $this->Html->script('vendors/jquery.cookie');
echo $this->Html->script('vendors/jquery.treeview');
echo $this->Html->script('vendors/jquery.tinyTips');
echo $this->Html->script('app');
?>
<script type="text/javascript">
//<![CDATA[
<?php if (DEBUG): ?>
var DEBUG = true;
<?php else: ?>
var DEBUG = false;
<?php endif; ?>
var base = '<?php echo $this->base; ?>';
var controller = '<?php echo $this->request->controller; ?>';
var action = '<?php echo $this->request->action; ?>';
//]]>
</script>
