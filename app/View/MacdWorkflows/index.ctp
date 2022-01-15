<?php echo $this->element('mds.nav');
echo $this->Html->script('macd_workflows/index');
echo $this->Html->css('macd_workflows/index'); ?>
<!-- MAIN --><div id="MAIN" class="clear" role="main">
<div id="MENU" role="complementary">

</div>
<div id="CONTENT">
    <?php if ($this->session->read('Auth.User.group_id') == 3) {
        echo $this->Form->button('新規月次報告を行う', array('id' => 'new_btn', 'type' => 'button')); 
    } ?>
    <p/>
    <?php if ($this->session->read('Auth.User.group_id') == 4 || $this->session->read('Auth.User.group_id') == 1 ) {?>
        <h2>月次報告設定</h2>
        <?php echo $this->Form->create('MacdWorkflow', array('type' => 'file')); ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
        <table id="template" border="0" cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse" >
            <th width="25%" align="left" bgcolor="#E7E7E7" style="font-weight: bold;">　月次報告テンプレート登録</th>
            <td width="60%"><?php $this->Attachment->input('CustomerOrganization', 'macdTemplate', array('comment' => false )); ?>
            </td>
            <td align="center">
            <?php echo $this->Form->button('登録', array('class' => 'save')); ?>
            </td>
        </table>
        <p/>
    <?php echo $this->Form->end();} ?>
    <h2><?php echo '月次報告一覧' ?></h2>
<div class="box">
<table class="data" summary="一覧">
<thead>
    <tr>
        <th width="18%"><?php echo $this->Paginator->sort('applied_title', $fieldnames['applied_title']); ?></th>
        <th width="12%"><?php echo $this->Paginator->sort('applied', $fieldnames['applied']); ?></th>
        <th width="15%"><?php echo $this->Paginator->sort('User.name', $fieldnames['applied_user_id']); ?></th>
        <th width="37%">報告書</th>
        <th width="8%"><?php echo $this->Paginator->sort('status', $fieldnames['status']); ?></th>
        <th width="11%">詳細</th>
    </tr>
</thead>
<tbody>
    <?php foreach($records as $record):
        $id = $record['MacdWorkflow']['id'];
        $applied_title = $record['MacdWorkflow']['applied_title'];
        $applied = $record['MacdWorkflow']['applied'];
        $applied_user_id = $record['MacdWorkflow']['applied_user_id'];
        $status = $record['MacdWorkflow']['status'];
        $this->Attachment->setData($record);
    ?>
    <tr>
        <td><?php echo h(trim($record['MacdWorkflow']['applied_title'])); ?></td>
        <td><?php 
            if (!empty($record['MacdWorkflow']['applied']) && substr($record['MacdWorkflow']['applied'], 0, 4) > '0000') {
                echo h(datetime_format($record['MacdWorkflow']['applied'])); 
            } ?></td>
        <td><?php echo h(trim($record['User']['name'])); ?></td>
        <td><ul><?php
        for ($j = 1; $j <= 5; $j++):
            if ($this->Attachment->is("aplForm{$j}")) {
                echo '<li>';
                $this->Attachment->link("aplForm{$j}", array('extension' => false));
                $this->Attachment->size("aplForm{$j}");
                echo '</li>';
            }
        endfor; ?></ul></td>
        <td><?php echo h(getStatusName(trim($record['MacdWorkflow']['status']))); ?></td>
        <td><?php
               if(trim($record['MacdWorkflow']['status']) == 1 && $this->session->read('Auth.User.group_id') == 3){
                  echo $this->Html->link('編集',
                    "/{$this->request->controller}/edit/{$id}/",
                    array('class' => 'detail')); 
               } else {
                  echo $this->Html->link('参照',
                    "/{$this->request->controller}/edit/{$id}/",
                    array('class' => 'detail')); 
               }
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
</div>

</div>
<!-- /MAIN --></div>

