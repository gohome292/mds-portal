<?php echo $this->element('menu_index'); ?>
<div class="hr"><hr /></div>
<?php echo $this->element('paginator'); ?>
<div class="list">
<table>
    <tr>
        <th>ファイル</th>
        <th><?php echo $fieldnames['size']; ?></th>
        <th><?php echo $fieldnames['created']; ?></th>
<?php echo $this->menu->remove_title(); ?>
    </tr>
<?php foreach ($records as $record):
$id = $record['Attachment']['id']; ?>
    <tr class="<?php echo $cycle->cycle('listOdd', 'listEven'); ?> record" id="<?php echo $id; ?>">
        <td><?php
        echo $this->Html->link(
            "{$record['Attachment']['alternative']}"
            . ".{$record['Attachment']['extension']}",
            "/iggy/attachments/download/{$record['Attachment']['id']}"
        ); ?></td>
        <td class="right"><?php echo filesize_format($record['Attachment']['size']); ?></td>
        <td><?php echo $record['Attachment']['created']; ?></td>
<?php echo $this->menu->remove_link($id); ?>
    </tr>
<?php endforeach; ?>
</table>
</div><!-- .list -->
