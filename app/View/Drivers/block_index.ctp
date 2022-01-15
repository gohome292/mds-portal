<?php 
echo $this->Html->script('drivers/block_index'); ?>
<div class="box">
<table class="data" summary="一覧">
<thead>
    <tr>
        <th width="100">設置場所</th>
        <th width="100">機器管理番号</th>
        <th width="200">ファイル</th>
        <th width="110">更新日</th>
        <th width="100">コメント</th>
    </tr>
</thead>
<tbody>
<?php foreach($records as $record):
    $id = $record['Driver']['id'];
    $place = $record['Driver']['place'];
    $kiki = $record['Driver']['kiki'];
    $modified = $record['Driver']['modified'];
    $comment = $record['Driver']['comment'];
    $this->Attachment->setData($record);
    ?>
    <tr>
        <th><?php echo h(trim($record['Driver']['place'])); ?></th>
        <th><?php echo h(trim($record['Driver']['kiki'])); ?></th>
        <td><ul><?php
        for ($j = 1; $j <= 20; $j++):
            if ($this->Attachment->is("file{$j}")) {
                echo '<li>';
                $this->Attachment->link("file{$j}", array('extension' => false));
                $this->Attachment->size("file{$j}");
                echo '</li>';
            }
        endfor; ?></ul></td>
        <th><?php echo h(datetime_format(trim($record['Driver']['modified']))); ?></th>
        <td class="mdscomment">
            <span title="<?php echo nl2br(h(trim($record['Driver']['comment']))); ?>"><?php
            echo h(mb_substr(
                preg_replace(
                    '/\s/',
                    '',
                    $record['Driver']['comment']
                ),
                0,
                Configure::read('Mds.reportCommentLength')
            ));
            if (Configure::read('Mds.reportCommentLength')
            < mb_strlen($record['Driver']['comment'])):
                echo '...';
            endif;
            ?></span>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
</div>
