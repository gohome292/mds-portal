<?php $id = $record['Information']['id'];
echo $this->element('menu_view', compact('id'));
$this->Attachment->setData($record); ?>
<div class="hr"><hr /></div>

<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['created']; ?></th>
        <td><?php echo $record['Information']['created']; ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td><?php echo $record['CustomerOrganization']['name']; ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['level']; ?></th>
        <td><?php echo $levels[$record['Information']['level']]; ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['regular']; ?></th>
        <td><?php echo $regulars[$record['Information']['regular']]; ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['title']; ?></th>
        <td><?php echo h($record['Information']['title']); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['content']; ?></th>
        <td><?php echo $this->richText->createLink(nl2br(h($record['Information']['content']))); ?></td>
    </tr>
    <tr>
        <th>添付ファイル</th>
        <td class="file"><?php
        for ($j = 1; $j <= 5; $j++):
            if ($this->Attachment->is("file{$j}")) {
                echo '<div>';
                $this->Attachment->link("file{$j}");
                $this->Attachment->size("file{$j}");
                echo '</div>';
            }
        endfor; ?></td>
    </tr>
</table>
</div><!-- .list -->
