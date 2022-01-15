<?php echo $this->Html->script('adm_drivers/block_index'); ?>
<input id="driver_file_count" type="hidden" value="<?php echo count($records); ?>" >
<table>
    <tr>
        <th>設置場所</th>
        <th>機器管理番号</th>
        <th>ファイル</th>
        <th>更新日</th>
        <th>コメント</th>
<?php echo $this->menu->edit_title();
echo $this->menu->remove_title(); ?>
    </tr>
<?php 
foreach($records as $record):
    $id = $record['Driver']['id'];
    $place = $record['Driver']['place'];
    $kiki = $record['Driver']['kiki'];
    $modified = $record['Driver']['modified'];
    $comment = $record['Driver']['comment'];
    $this->Attachment->setData($record);
?>
    <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?> record">
        <td class="top"><?php echo h(trim($record['Driver']['place'])); ?></td>
        <td class="top"><?php echo nl2br(h(trim($record['Driver']['kiki']))); ?></td>
        <td class="top file"><?php
        for ($j = 1; $j <= 20; $j++):
            if ($this->Attachment->is("file{$j}")) {
                echo '<div>';
                $this->Attachment->link("file{$j}", array('extension' => false));
                $this->Attachment->size("file{$j}");
                echo '</div>';
            }
        endfor; ?></td>
        <td class="top"><?php echo h(datetime_format(trim($record['Driver']['modified']))); ?></td>
        <td class="top mdscomment">
            <span title="<?php echo nl2br(h(trim($record['Driver']['comment']))); ?>"><?php
            echo nl2br(mb_substr(
                h(trim($record['Driver']['comment'])),
                0,
                Configure::read('Mds.reportCommentLength')
            ));
            if (Configure::read('Mds.reportCommentLength')
            < mb_strlen($record['Driver']['comment'])):
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
                    'driver_id' => $id,
                )
            );
            ?>
        </td>
        <?php echo $this->menu->remove_link($id); ?>
    </tr>
<?php endforeach; ?>
</table>
