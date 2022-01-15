<?php echo $this->element('menu_index'); ?>
<div class="hr"><hr /></div>
<div class="tree" style="display: none;">
<?php
if (!empty($records)) {
    echo $this->Tree->run($records, array('controller' => true, 'action' => 'edit'));
}
?>
</div><!-- .list -->
