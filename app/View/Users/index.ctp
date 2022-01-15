<?php echo $this->element('header_search');
echo $this->element('menu_index');
echo $this->Tab->run(array(
    'list'   => '一覧',
    'search' => '検索',
)); ?>

<div class="hidden" id="search_area">
<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'index'))); ?>
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['name']; ?></th>
        <td><?php echo $this->Form->input('name', array('label' => false, 'div' => false,)); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['group_id']; ?></th>
        <td><?php echo $this->Form->input('group_id', array('empty' => true, 'label' => false, 'div' => false,)); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td><?php
        $sel = '';
        if (isset($this->request->data['User']['top_customer_organization_id']))
            $sel = @$this->request->data['User']['top_customer_organization_id'];
        echo $this->Form->input(
            'top_customer_organization_id',
            array(
                'type' => 'select',
                'options' => $customer_organizations,
                'empty'   => true,
                'selected' => $sel,
                'label' => false,    // labelを出力しない
                'div' => false,    // labelを出力しない
            )
        );
        ?></td>
    </tr>
    <tr>
        <td colspan="2" class="submit right"><?php echo $this->Form->submit('検　索'); ?>
        <input type="button" value="検索解除" id="cancel_button" /></td>
    </tr>
</table>
</div><!-- .search -->
<?php echo $this->Form->end(); ?>
</div><!-- #search_area -->

<div id="list_area">
<?php echo $this->element('paginator'); ?>
<div class="list">
<table>
    <tr>
        <th><?php echo $this->Paginator->sort('username',$fieldnames['username']); ?></th>
        <th><?php echo $this->Paginator->sort('name',$fieldnames['name']); ?></th>
        <th><?php echo $this->Paginator->sort('group_id',$fieldnames['group_id']); ?></th>
        <th><?php echo $this->Paginator->sort('CustomerOrganization.lft','第1階層'); ?></th>
        <th>第2階層</th>
        <th>第3階層</th>
        <th>第4階層</th>
        <th>第5階層</th>
<?php echo $this->menu->edit_title();
echo $this->menu->remove_title(); ?>
    </tr>
<?php foreach ($records as $record):
$id = $record['User']['id'];
if (isset($record['CustomerOrganization']['path'])) {
  $path = explode(' > ',
    $record['CustomerOrganization']['path']);
} else {
  $path = array();
}
?>
    <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?> record" id="<?php echo $id; ?>">
        <td><?php echo h($record['User']['username']); ?></td>
        <td><?php echo $this->menu->view($record['User']['name'], $id); ?></td>
        <td><?php echo h($record['Group']['name']); ?></td>
        <td><?php echo h(@$path[0]); ?></td>
        <td><?php echo h(@$path[1]); ?></td>
        <td><?php echo h(@$path[2]); ?></td>
        <td><?php echo h(@$path[3]); ?></td>
        <td><?php echo h(@$path[4]); ?></td>
<?php echo $this->menu->edit_link($id);
echo $this->menu->remove_link($id); ?>
    </tr>
<?php endforeach; ?>
</table>
</div><!-- .list -->
</div><!-- #list_area -->
</div><!-- #tabs -->
