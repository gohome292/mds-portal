<?php echo $this->element('menu_refer'); ?>
<div class="tree" style="display: none;">
<?php
if (!empty($records)) {
    echo $this->Tree->run($records, array('controller' => true, 'refer' => true));
}
?>
</div><!-- .list -->
