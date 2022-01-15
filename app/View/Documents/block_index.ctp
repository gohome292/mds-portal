<?php echo $this->Html->script('guest/base');
echo $this->Html->script('documents/block_index'); ?>
<h2><?php echo $customer_organization_name; ?></h2>
<div class="box">
<table class="data" summary="一覧">
<thead>
    <tr>
        <th>名前</th>
        <th>報告書</th>
        <th>コメント</th>
    </tr>
</thead>
<tbody>
    <?php foreach($records as $record):
    $id = $record['Document']['id'];
    $this->Attachment->setData($record); ?>
    <tr>
        <th><?php echo h($record['CustomerOrganization']['name']); ?></th>
        <td><ul><?php
        for ($j = 1; $j <= 5; $j++):
            if ($this->Attachment->is("file{$j}")) {
                echo '<li>';
                $this->Attachment->link("file{$j}", array('extension' => false));
                $this->Attachment->size("file{$j}");
            }
        endfor; ?></ul></td>
        <td class="mdscomment">
            <span title="<?php echo nl2br(h(trim($record['Document']['comment']))); ?>"><?php
            echo h(mb_substr(
                preg_replace(
                    '/\s/',
                    '',
                    $record['Document']['comment']
                ),
                0,
                Configure::read('Mds.reportCommentLength')
            ));
            if (Configure::read('Mds.reportCommentLength')
            < mb_strlen($record['Document']['comment'])):
                echo '...';
            endif;
            ?></span>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
</div>
