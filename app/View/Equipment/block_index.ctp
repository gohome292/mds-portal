<?php echo $this->Html->script('guest/base'); ?>
<h2><?php echo $customer_organization_name; ?></h2>
<div class="box">
<table class="data" summary="一覧">
<thead>
    <tr>
        <th>名前</th>
        <th>機器管理情報</th>
    </tr>
</thead>
<tbody>
    <?php foreach($records as $record):
    $id = $record['Equipment']['id'];
    $this->Attachment->setData($record); ?>
    <tr>
        <th><?php echo h($record['CustomerOrganization']['name']); ?></th>
        <td><ul><?php
        for ($j = 1; $j <= 2; $j++):
            if ($this->Attachment->is("file{$j}")) {
                echo '<li>';
                $this->Attachment->link("file{$j}", array('extension' => false));
                $this->Attachment->size("file{$j}");
                echo ' ';
                $this->Attachment->modified(
                    "file{$j}",
                    array(
                        'mode' => 'JD',
                        'format' => false,
                    )
                );
                echo '更新</li>';
            }
        endfor; ?></ul></td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
</div>
