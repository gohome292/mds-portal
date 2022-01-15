<?php
echo $this->Html->css('reset');
echo $this->Html->css('app');
echo $this->Html->css('class');
echo $this->Html->css('vendors/jquery.treeview');
echo $this->Html->css('vendors/jquery.tinyTips');
if (DEBUG && isset($this->request->named['debug'])) {
    echo $this->Html->css('debug');
}
