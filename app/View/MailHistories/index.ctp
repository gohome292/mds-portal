<?php echo $this->element('header_search');
if (Configure::read('App.screen.compact')):
echo $this->Html->css('elements/compact.index');
endif; ?>
<div class="menu horizon">
<ul>
<?php
echo '<li id="menu_mail">' . $this->Html->link(
    $this->Html->image('menu/mail.gif'),
    "/{$this->request->controller}/address/",
    array('escape' => false)
) . '</li>';
echo '<li id="menu_renew">' . $this->Html->link(
    $this->Html->image('menu/renew.gif'),
    "/{$this->request->controller}/{$this->request->action}/",
    array('escape' => false)
) . '</li>';
?>
</ul>
</div><!-- .menu -->
<?php echo $this->Tab->run(array(
    'list'   => '一覧',
    'search' => '検索',
)); ?>

<div class="hidden" id="search_area">
<?php echo $this->Form->create('MailHistory', array('url' => array('controller' => 'mail_histories', 'action' => 'index')));?>
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td><?php echo $this->Form->input(
            'customer_organization_id',
            array(
                'options' => $customer_organizations,
                'empty'   => true,
                'label' => false,    // labelを出力しない
                'div' => false,      // divで囲わない
            )
        ); ?></td>
    </tr>
    <tr>
        <td colspan="2" class="submit right"><?php echo $this->Form->submit('検　索'); ?>
        <input type="button" value="検索解除" id="cancel_button" /></td>
    </tr>
</table>
</div><!-- .search -->
<?php echo $this->Form->end(); ?>
</div><!-- #search_area -->

<div id="list_area">
<?php echo $this->element('paginator');?>
<div class="list">
<table width="100%">
    <tr>
        <th><?php echo $this->Paginator->sort('send_start_date', $fieldnames['send_start_date']); ?></th>
        <th><?php echo $this->Paginator->sort('send_end_date', $fieldnames['send_end_date']); ?></th>
        <th><?php echo $this->Paginator->sort('user_id',$fieldnames['user_id']); ?></th>
        <th><?php echo $this->Paginator->sort('CustomerOrganization.lft', $fieldnames['customer_organization_id']); ?></th>
        <th width="40px"><?php echo $this->Paginator->sort('send_order_count', $fieldnames['send_order_count']); ?></th>
        <th width="40px"><?php echo $this->Paginator->sort('success_count', $fieldnames['success_count']); ?></th>
        <th width="40px"><?php echo $this->Paginator->sort('failure_count', $fieldnames['failure_count']); ?></th>
        <th width="40px">成功<br/>リスト</th>
        <th width="40px">失敗<br/>リスト</th>
    </tr>
<?php foreach ($records as $record):
$id = $record['MailHistory']['id']; ?>
    <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?> record" id="<?php echo $id; ?>">
        <td><?php echo $record['MailHistory']['send_start_date']; ?></td>
        <td><?php echo $record['MailHistory']['send_end_date']; ?></td>
        <td><?php echo h($record['cUser']['name']); ?></td>
        <td><?php echo h($record['CustomerOrganization']['name']);
           if (empty($record['MailHistory']['template_seq'])) {
               echo h('(t01)');
           } else if ($record['MailHistory']['template_seq']<10) {
               echo h('(t0'.$record['MailHistory']['template_seq'].')');
           } else {
               echo h('(t'.$record['MailHistory']['template_seq'].')');
           }
        ?></td>
        <td class="numeric"><?php echo $record['MailHistory']['send_order_count']; ?></td>
        <td class="numeric"><?php echo $record['MailHistory']['success_count']; ?></td>
        <td class="numeric"><?php echo $record['MailHistory']['failure_count']; ?></td>
        <td class="center"><?php
        if ($record['MailHistory']['success_count'] > 0):
            echo $this->Html->link(
                '確認',
                "/{$this->request->controller}/output/{$id}/1/"
            );
        else:
            echo '&nbsp;';
        endif;
        ?></td>
        <td class="center"><?php
        if ($record['MailHistory']['failure_count'] > 0):
            echo $this->Html->link(
                '確認',
                "/{$this->request->controller}/output/{$id}/2/"
            );
        else:
            echo '&nbsp;';
        endif;
        ?></td>
    </tr>
<?php endforeach; ?>
</table>
</div><!-- .list -->
</div><!-- #list_area -->
</div><!-- #tabs -->
