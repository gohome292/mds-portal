<?php echo $this->Html->script('elements/edit');
if (Configure::read('App.screen.compact')):
echo $this->Html->css('elements/compact.edit');
endif; ?>
<div class="menu horizon">
<ul>
<?php echo $this->menu->back(); ?>
</ul>
</div><!-- .menu -->
<div class="hr"><hr /></div>
<?php echo $this->Form->create('MailHistory', array('url' => array('controller' => 'mail_histories', 'action' => 'confirm'))); ?>
<div class="list">
<table  width="100%">
    <tr>
        <th style="width: 80px;"><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td><input type="text" name="txtFindCust" size="50" id="txtFindCust">
            <input type="button" name="btnFindCust" id="btnFindCust" value="絞込み">
        <br/>
        <?php echo $this->Form->input(
            'customer_organization_id',
            array(
                'options' => $customer_organizations,
                'div'     => false,
                'label'   => false,
            )
        ); ?></td>
    </tr>
    <?php if (!empty($this->request->data['MailHistory']['customer_organization_id']) && 
              $this->request->data['MailHistory']['customer_organization_id']>0) {
    ?>
    <tr>
        <th>テンプレート<span class="required_mark">*</span></th>
        <td>
            <div style="margin-bottom:8px">メールリスト取込用ID：　<input type="text" id="tId" name="tId" readonly="true" value="" size="3"/></div>
            <?php echo $this->Form->input(
                'template_id',
                array(
                    'options' => $mail_templates,
                    'empty'   => false,
                    'div'     => false,
                    'label'   => false,
                )
            );?>
        </td>
    </tr>
    <tr>
        <td colspan="2" ><br/><span style="margin-top:10px" id="customer_organization_name">&nbsp;</span>
           <span style="float:right" class="submit"><input class="confirm" type="submit" value="送信確認" style="width:120px;"/></span></td>
    </tr>
    <?php } ?>
</table>

<table width="100%">
    <tr>
        <th><?php echo $this->Paginator->sort('plan_start', $fieldnames['plan_start']); ?></th>
        <th><?php echo $this->Paginator->sort('customer_organization_id', $fieldnames['customer_organization_id']); ?></th>
        <th><?php echo $this->Paginator->sort('user_id', $fieldnames['user_id']); ?></th>
        <th width="60px"><?php echo $this->Paginator->sort('template_seq', $fieldnames['template_seq']); ?></th>
        <th><?php echo $this->Paginator->sort('modified_user_id', $fieldnames['modified_user_id']); ?></th>
        <th><?php echo $this->Paginator->sort('modified', $fieldnames['modified']); ?></th>
        <th width="40px"><?php echo $this->Paginator->sort('send_order_count', $fieldnames['send_order_count']); ?></th>
        <th width="50px">確認</th>
        <th width="40px">削除</th>
    </tr>
<?php foreach ($records as $record):
$id = $record['MailHistory']['id']; ?>
    <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?> record" id="<?php echo $id; ?>">
        <td><?php echo $record['MailHistory']['plan_start']; ?></td>
        <td><?php echo $record['CustomerOrganization']['name']; ?></td>
        <td><?php echo h($record['cUser']['name']); ?></td>
        <td><?php if (empty($record['MailHistory']['template_seq'])) {
               echo h('t01');
           } else if ($record['MailHistory']['template_seq']<10) {
               echo h('t0'.$record['MailHistory']['template_seq']);
           } else {
               echo h('t'.$record['MailHistory']['template_seq']);
           }?></td>
        <td><?php echo h($record['mUser']['name']); ?></td>
        <td><?php echo $record['MailHistory']['modified'];?></td>
        <td class="numeric"><?php echo $record['MailHistory']['send_order_count']; ?></td>
        <td class="center"><?php if (empty($record['MailHistory']['send_start_date'])) {
                echo $this->Html->link('確認',"/{$this->request->controller}/confirm/{$id}/");
            } else {
                echo '送信中';
            }?>
        </td>
        <td class="center"><?php if (empty($record['MailHistory']['send_start_date'])) {
                echo $this->Html->link('削除',"/{$this->request->controller}/delete/{$id}/");
            }?>
        </td>
    </tr>
<?php endforeach; ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
