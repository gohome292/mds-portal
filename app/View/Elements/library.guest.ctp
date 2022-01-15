<?php
echo $this->Html->script('vendors/jquery');
echo $this->Html->script('vendors/jquery.cookie');
echo $this->Html->script('vendors/jquery.treeview');
echo $this->Html->script('vendors/jquery.tinyTips');
echo $this->Html->script('app');

echo $this->Html->css('vendors/jquery.treeview');
echo $this->Html->css('vendors/jquery.tinyTips');
?>
<script type="text/javascript">
//<![CDATA[
var base = '<?php echo $this->base; ?>';
var controller = '<?php echo $this->request->controller; ?>';
var action = '<?php echo $this->request->action; ?>';
var ajax_loader_image =
    '<center><img src="' + base + '/img/ajax-loader.gif"></center>';
//]]>
</script>
<?php
echo $this->Html->script('guest/base');
echo $this->Html->css('guest/base');
?>
