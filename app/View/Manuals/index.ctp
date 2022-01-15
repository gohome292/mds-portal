<?php echo $this->element('mds.nav');
echo $this->Html->script('manuals/index');
?>

<!-- MAIN --><div id="MAIN" class="clear" role="main">
<div id="MENU" role="complementary">

</div>
<div id="CONTENT">
    <!-- 結果表示 -->
<?php echo $this->Form->create(); ?>
    <table>
        <tr>
            <!--<th>分類リスト</th>-->
            <td><?php echo $this->Form->input(
                'driver_manual_type_id', array(
                'type' => 'select',
                'options' => $type_list,
                'selected' => $this->request->data['driver_manual_type_id'],
                'style'=>'width:250px;height:25px;margin-bottom:18px',
                'label' => false,    // labelを出力しない
                'div' => false,      // divで囲わない
                'empty' => false,)
            ); ?></td>
	    </tr>
	</table>
    <h2><?php echo 'マニュアル一覧'; ?></h2>
    <div id="MANUALLIST">
<table class="data" summary="一覧">
<thead>
    <tr>
        <th><?php echo $this->Paginator->sort('type', $fieldnames['type']); ?></th>
        <th><?php echo $this->Paginator->sort('category', $fieldnames['category']); ?></th>
        <th>ファイル</th>
        <th><?php echo $this->Paginator->sort('modified', $fieldnames['modified']); ?></th>
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
<?php echo $this->Form->end(); ?>
</div>
<!-- /MAIN --></div>
