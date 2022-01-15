<?php echo $this->Html->script('adm_documents/block_index'); ?>
<table>
    <tr>
        <th><?php echo $fieldnames['customer_organization_id']; ?></th>
        <th>報告書</th>
        <th><?php echo $fieldnames['comment']; ?></th>
<?php echo $this->menu->edit_title();
echo $this->menu->remove_title(); ?>
    </tr>
<?php foreach ($parent_records as $parent_record):
$i = $parent_record['CustomerOrganization']['id']; // customer_organization_id
if (empty($records[$i])):?>
    <tr class="<?php echo $this->cycle->cycle('listOdd', 'listEven'); ?> record">
        <td><?php echo h($parent_record['CustomerOrganization']['name']); ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="center">
            <?php
            echo $this->Html->link(
                '編集',
                'javascript: void(0);',
                array(
                    'class' => 'edit_link',
                    'customer_organization_id' => $i,
                )
            );
            ?>
        </td>
        <?php if (!empty($acl['remove'])): ?>
        <td>&nbsp;</td>
        <?php endif; ?>
    </tr>
<?php else:
$id = $records[$i]['Document']['id'];
$this->Attachment->setData($records[$i]); ?>
    <tr class="<?php echo $this->cycle->cycle('listOdd', 'listEven'); ?> record" id="<?php echo $id; ?>">
        <td class="top"><?php echo h($records[$i]['CustomerOrganization']['name']); ?></td>
        <td class="top file"><?php
        for ($j = 1; $j <= 5; $j++):
            if ($this->Attachment->is("file{$j}")) {
                echo '<div>';
                $this->Attachment->link("file{$j}");
                $this->Attachment->size("file{$j}");
                echo '</div>';
            }
        endfor; ?></td>
        <td class="top mdscomment">
            <span title="<?php echo nl2br(h(trim($records[$i]['Document']['comment']))); ?>"><?php
            echo h(mb_substr(
                preg_replace(
                    '/\s/',
                    '',
                    $records[$i]['Document']['comment']
                ),
                0,
                Configure::read('Mds.reportCommentLength')
            ));
            if (Configure::read('Mds.reportCommentLength')
            < mb_strlen($records[$i]['Document']['comment'])):
                echo '...';
            endif;
            ?></span>
        </td>
        <td class="edit center">
            <?php
            echo $this->Html->link(
                '編集',
                'javascript: void(0);',
                array(
                    'class' => 'edit_link',
                    'customer_organization_id' => $i,
                )
            );
            ?>
        </td>
<?php echo $this->menu->remove_link($id); ?>
    </tr>
<?php endif;
endforeach; ?>
</table>
<br/><br/><br/>
<div align="right">
    <input type="button" class="ret_link" value="戻る"/>
</div>
