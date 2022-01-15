<?php echo $this->element('header_search');
echo $this->element('menu_index');
echo $this->Tab->run(array(
    'list'   => '一覧',
    'search' => '検索',
)); ?>

<div class="hidden" id="search_area">
<?php echo $this->Form->create('AdmInformation', array('url' => array('controller' => 'adm_informations', 'action' => 'index'))); ?>
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td><?php
        echo $this->Form->input(
            'Information.customer_organization_id',
            array(
                'options' => $customer_organizations,
                'empty'   => '',
                'label' => false,    // labelを出力しない
                'div' => false,      // divで囲わない
            )
        );
        ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['level']; ?></th>
        <td><?php
        echo $this->Form->input(
            'Information.level',
            array(
                'options' => $levels,
                'empty'   => '',
                'label' => false,    // labelを出力しない
                'div' => false,      // divで囲わない
            )
        );
        ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['regular']; ?></th>
        <td class="regular"><?php
        echo $this->Form->input(
            'Information.regular',
            array(
                'options' => $regulars,
                'empty'   => '',
                'type'    => 'radio',
                'label' => false,    // labelを出力しない
                'div' => false,      // divで囲わない
            )
        );
        ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['title']; ?></th>
        <td><?php echo $this->Form->text('Information.title'); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['content']; ?></th>
        <td><?php echo $this->Form->text('Information.content'); ?></td>
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
        <th><?php echo $this->Paginator->sort('created', $fieldnames['created']); ?></th>
        <th><?php echo $this->Paginator->sort('CustomerOrganization.lft', $fieldnames['customer_organization_id']); ?></th>
        <th><?php echo $this->Paginator->sort('level', $fieldnames['level']); ?></th>
        <th><?php echo $this->Paginator->sort('regular', $fieldnames['regular']); ?></th>
        <th><?php echo $this->Paginator->sort('title', $fieldnames['title']); ?></th>
<?php echo $this->menu->edit_title();
echo $this->menu->remove_title(); ?>
    </tr>
<?php foreach ($records as $record):
$id = $record['Information']['id']; ?>
    <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?> record" id="<?php echo $id; ?>">
        <td><?php echo $record['Information']['created']; ?></td>
        <td><?php echo h($record['CustomerOrganization']['name']); ?></td>
        <td class="center"><?php echo $levels[$record['Information']['level']]; ?></td>
        <td class="center"><?php echo $record['Information']['regular'] == 1 ? '*' : ''; ?></td>
        <td><?php echo $this->menu->view($record['Information']['title'], $id); ?></td>
<?php echo $this->menu->edit_link($id);
echo $this->menu->remove_link($id); ?>
    </tr>
<?php endforeach; ?>
</table>
</div><!-- .list -->
</div><!-- #list_area -->
</div><!-- #tabs -->
