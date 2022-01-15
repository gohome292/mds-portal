<?php echo $this->Html->script('elements/edit');
echo $this->Form->create('MailTemplate', array('type' => 'file',)); ?>
<div class="list">
<table>
    <tr>
        <th>対象顧客</th>
        <td><input type="text" name="txtFindCust" size="50" id="txtFindCust">
            <input type="button" name="btnFindCust" id="btnFindCust" value="絞込み">
        <br/>
          <?php echo $this->Form->input(
            'customer_organization_id',
            array(
                'options' => $customer_organizations,
                'empty'   => false,
                'div'     => false,
                'label'   => false,
            )
        );
        echo '<span class="submit">' . $this->Form->button(
            'メールリスト出力',
            array(
                'id' => 'mail_comment_output',
                'type'  => 'button',
            )
        ) . '</span>'; ?></td>
    </tr>
    <tr>
        <th>テンプレート<span class="required_mark">*</span></th>
        <td><table width="624">
            <tr>
            <td style="margin-bottom:8px">メールリスト取込用ID：　<input type="text" id="tId" name="tId" disabled="true" readonly="true" value="" size="3"/></td>
            <td align="right"><input type="checkbox" id="new_flg" name="new_flg" value="1">新規作成</td>
            </tr>
            </table>
            <?php echo $this->Form->input(
                'id',
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
        <th>件名<span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('title', array('div' => false, 'label' => false)); ?></td>
    </tr>
    <tr>
        <th>本文<span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('body', array('div' => false, 'label' => false)); ?>
        <div><span class="comment">以下の文字列がお客様の条件毎に動的に変わります。文字列は件名及び本文で利用できます。</span>
        <table id="legend">
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:company_name</th>
                <td>お客様の表示用会社名</td>
                <th>:organization_name</th>
                <td>お客様の直近の組織名<br />
                （第1階層の場合は空白）</td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:person_name</th>
                <td>お客様の表示用氏名</td>
                <th>:comment</th>
                <td>顧客組織毎のコメント<span class="blue">※</span></td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:freeword1</th>
                <td>自由項目1（100文字以内）<span class="blue">※</span></td>
                <th>:freeword2</th>
                <td>自由項目2（100文字以内）<span class="blue">※</span></td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:last_month</th>
                <td>先月[<?php echo date('Y年n月', strtotime('-1 month')); ?>]</td>
                <th>:this_month</th>
                <td>今月[<?php echo date('Y年n月'); ?>]</td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:information_url</th>
                <td colspan="3">お知らせURL
                [<?php echo $this->base . '/information/index/'; ?>]</td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:documents_last_month_url</th>
                <td colspan="3">先月の報告書URL
                [<?php echo $this->base . '/documents/index/' . date('Ym', strtotime('-1 month')); ?>]</td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:documents_this_month_url</th>
                <td colspan="3">今月の報告書URL
                [<?php echo $this->base . '/documents/index/' . date('Ym'); ?>]</td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:equipment_url</th>
                <td colspan="3">機器管理情報URL
                [<?php echo $this->base . '/equipment/index/'; ?>]</td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:driver_url</th>
                <td colspan="3">プリンタドライバー情報URL
                [<?php echo $this->base . '/drivers/index/'; ?>]</td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:manual_url</th>
                <td colspan="3">マニュアル情報URL
                [<?php echo $this->base . '/manuals/index/'; ?>]</td>
            </tr>
            <tr class="<?php echo $this->Cycle->cycle('listOdd', 'listEven'); ?>">
                <th>:macd_workflow_url</th>
                <td colspan="3">MACD申請情報URL
                [<?php echo $this->base . '/macd_workflows/index/'; ?>]</td>
            </tr>
        </table>
        <span class="blue">※</span><span class="comment">メールリストより取り込む項目です。</span><br>
        <span class="comment">【注意】半角カタカナ及び、機種依存文字を使用するとメール本文で文字化けします。</span>
        </div></td>
    </tr>
    <tr>
        <th>メールリスト取込</th>
        <td><div class="comment">指定できるファイルの拡張子は、[csv]です。</div>
        <?php $this->Attachment->input(
            'MailTemplate',
            'comment',
            array(
                'existing' => false,
                'comment'  => false,
            )
        ); ?></td>
    </tr>
<?php echo $this->element('edit_common_area'); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
