<?php 
echo $this->Html->script('manuals/block_index'); ?>
<div class="box">
<table class="data" summary="一覧">
<thead>
    <tr>
        <th>タイプ</th>
        <th>カテゴリ</th>
        <th>ファイル</th>
        <th>更新日</th>
        <th>コメント</th>
    </tr>
</thead>
<tbody>
<?php foreach($records as $record):
    $id = $record['Manual']['id'];
    $place = $record['Manual']['type'];
    $kiki = $record['Manual']['category'];
    $modified = $record['Manual']['modified'];
    $comment = $record['Manual']['comment'];
    $this->Attachment->setData($record); ?>
    <tr>
        <th><?php echo h(trim($record['Manual']['type'])); ?></th>
        <th><?php echo h(trim($record['Manual']['category'])); ?></th>
        <td><ul><?php
        for ($j = 1; $j <= 20; $j++):
            if ($this->Attachment->is("file{$j}")) {
                echo '<li>';
                $this->Attachment->link("file{$j}", array('extension' => false));
                $this->Attachment->size("file{$j}");
                echo '</li>';
            }
        endfor; ?></ul></td>
        <th><?php echo h(datetime_format(trim($record['Manual']['modified']))); ?></th>
        <td class="mdscomment">
            <span title="<?php echo nl2br(h(trim($record['Manual']['comment']))); ?>"><?php
            echo h(mb_substr(
                preg_replace(
                    '/\s/',
                    '',
                    $record['Manual']['comment']
                ),
                0,
                Configure::read('Mds.reportCommentLength')
            ));
            if (Configure::read('Mds.reportCommentLength')
            < mb_strlen($record['Manual']['comment'])):
                echo '...';
            endif;
            ?></span>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
</div>
