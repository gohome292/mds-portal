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
<?php echo $this->Form->create('MailHistory', array('url' => array('controller' => 'mail_histories', 'action' => 'confirm'))); 
echo $this->Form->input('id');?>
<div class="list">
<table with="100%">
    <tr>
        <th width="70px"><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td width="150px"><?php echo $this->request->data['CustomerOrganization']['name'];
                  echo $this->Form->hidden('customer_organization_id');
                  echo $this->Form->hidden('template_id');
                  echo $this->Form->hidden('template_seq');
                  echo $this->Form->hidden('user_id');
                  echo $this->Form->hidden('modified_user_id');?>
        </td>
        <th width="120px"><?php echo $fieldnames['user_id']; ?></th>
        <td width="180px"><?php echo h($this->request->data['cUser']['name']); ?></td>
        <th width="100px"><?php echo $fieldnames['modified_user_id']; ?></th>
        <td width="140px"><?php echo h($this->request->data['mUser']['name']); ?></td>
    </tr>
    <tr>
        <th>メール件数</th>
        <td><?php echo $this->request->data['MailHistory']['send_order_count'];
            echo $this->Form->hidden('send_order_count');
            echo $this->Form->hidden('open_flag');?>
            <span id='auth_group_id' style="display:none"><?php echo $this->session->read('Auth.User.group_id');?></span>
            <span id='count0' style="display:none"><?php echo $count0;?></span>
            <span id='count1' style="display:none"><?php echo $count1;?></span>
            <span id='count2' style="display:none"><?php echo $count2;?></span>
            <span class="submit">
            <?php if (!empty($this->request->data['MailHistory']['id'])) {?>
                <input type="button" value="送信者リスト" style="width:100px;"
                onclick="location.href='<?php echo h($this->base.'/mail_histories/output/'.$this->request->data['MailHistory']['id'].'/');?>';"/>
            <?php } else { ?>
                <input type="button" value="送信者リスト" style="width:100px;"
                onclick="location.href='<?php echo h($this->base.'/mail_histories/output/0/'.$this->request->data['MailHistory']['template_id'].'/');?>';"/>
            <?php } ?>
            </span>
        </td>
        <th><?php echo $fieldnames['plan_start']; ?><br/>
            （年月日／時間24h）</th>
        <td><input type="checkbox" name="sendmailFlg" id="sendmailFlg" label="予約"/>
            <label for="sendmailFlg">メール予約</label><br/>
            <?php echo $this->Form->datetime('plan_start',
            'YMD', 
            24,
            Array(
                'minYear' => date('Y'),
                'maxYear' => date('Y')+1,
                'separator' => ' ',
                'empty' => false,
                'monthNames' => false,
                'interval' => 60,
            )
        );?>
        </td>
        <th><?php echo $fieldnames['modified']; ?></th>
        <td><?php echo h($this->request->data['MailHistory']['modified']); ?></td>
    </tr>
    <tr>
        <td colspan="2"><span class="comment">指定時間でバッチが送信します。１回<br/><?php echo MAX_MAIL_COUNT_30MINUTES; ?>通までメールを送信した後、5分<br/>スリップしますので、大量送信した場合、<br/>届く時間が違うことを了承してください。</span></td>
        <th>毎月定期送信</th>
        <td>
           <?php echo $this->Form->hidden('sendflg');?>
           <input type="checkbox" name="mailLoopFlg" id="mailLoopFlg" />
           <label for="mailLoopFlg">定期送信</label><br/>
           毎月<?php echo $this->Form->datetime('fix_send_date',
            'D', 
            24,
            Array(
                'minYear' => date('Y'),
                'maxYear' => date('Y')+1,
                'separator' => ' ',
                'empty' => false,
                'monthNames' => false,
                'interval' => 60,
            )
        );?>
        </td>
        <td>
           報告書の存在チェック：<br/>
           <?php echo $this->Form->input(
                'document_check', array(
                    'type' => 'select',
                    'options' => $document_check_list,
                    'label' => false,    // labelを出力しない
                    'div' => false,      // divで囲わない
                    'empty' => false,)
            ); ?>
        </td>
        <td class="right">
            <input id="reserve" type="button" value="送信予約する" disabled="disabled" style="width:120px;"/>
            <input id="sendmail" type="button" value="送信する" style="width:120px;"/>
            </div></td>
    </tr>
</table><br />
<table id="mail_sample" width="100%">
    <tr>
        <th colspan="2">（例）メール1通目</th>
    </tr>
    <tr>
        <th style="width: 80px;">To</th>
        <td><?php echo h($mail_sample['MailHistoryDetail']['to']); ?></td>
    </tr>
    <tr>
        <th>From</th>
        <td><?php echo h($mail_sample['MailHistoryDetail']['from']); ?></td>
    </tr>
    <tr>
        <th>件名</th>
        <td><?php echo h($mail_sample['MailHistoryDetail']['title']); ?></td>
    </tr>
    <tr>
        <th>本文</th>
        <td><?php echo nl2br(h($mail_sample['MailHistoryDetail']['body'])); ?></td>
    </tr>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
