<?php echo $this->Html->script('users/mps_sel'); ?>
<?php echo $this->Html->script('elements/edit');
echo $this->element('menu_edit'); ?>
<div class="hr"><hr /></div>
<?php echo $this->Form->create('User', array('onsubmit' => 'makeCustSel()'));
echo $this->Form->input('id'); ?>
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['username']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('username', array('class' => 'ime_off', 'div' => false, 'label' => false)); ?>
        <div class="comment">ログイン時に使用します。半角3文字以上で入力してください。</div></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['name']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('name', array('class' => 'ime_on', 'div' => false, 'label' => false)); ?>
        <div class="comment">姓名を入力してください。</div></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['group_id']; ?><span class="required_mark">*</span></th>
        <td><?php if (isset($this->request->data['User']['id'])
        && $this->request->data['User']['id'] == $this->session->read('Auth.User.id')):
        $group_id = $this->Form->data['User']['group_id'];
        echo h($groups[$group_id]);
        echo $this->Form->input('group_id', array('type' => 'hidden'));
        else:
        echo $this->Form->input('group_id', array('div' => false, 'label' => false)); ?>
        <div class="comment">この選択によりこのシステムの操作権限が変化します。</div>
        <?php endif; ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['password']; ?><?php if ($this->request->action == 'add') echo '<span class="required_mark">*</span>'; ?></th>
        <td><?php echo $this->Form->input('password1', array('type' => 'password', 'div' => false, 'label' => false)); ?>
        <div class="comment">ログイン時に使用します。セキュリティを考慮して、8文字以上を入力してください。</div></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['password']; ?>確認<?php if ($this->request->action == 'add') echo '<span class="required_mark">*</span>'; ?></th>
        <td><?php echo $this->Form->input('password2', array('type' => 'password', 'div' => false, 'label' => false)); ?>
        <div class="comment">確認の為、再度同じ<?php echo $fieldnames['password']; ?>を入力してください。</div></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['email']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('email', array('class' => 'ime_off', 'div' => false, 'label' => false)); ?>
        <div class="comment">メール一括送信に使用されます。</div></td>
    </tr>
    <tr id="customer_organization">
        <th><?php echo $fieldnames['customer_organization_id']; ?><span class="required_mark">*</span></th>
        <td id="ReferCustomerOrganization"><?php
        echo $this->Form->text('CustomerOrganization.path', array('readonly' => 'true', 'div' => false, 'label' => false));
        echo $this->Form->button('参照...', array('type' => null));
        echo $this->Form->hidden('customer_organization_id');
        if (!empty($errors['customer_organization_id'])) {
            echo "<div class=\"failure\">{$errors['customer_organization_id']}</div>";
        }
        ?></td>
    </tr>
    <tr id="company_name_for_mail">
        <th><?php echo $fieldnames['company_name_for_mail']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('company_name_for_mail', array('div' => false, 'label' => false)); ?>
        <div class="comment">ログイン後の画面右上の名称表示やメール本文に使用されます。</div>
        <div class="comment">【注意】半角カタカナ及び、機種依存文字を使用するとメール本文で文字化けします。</div></td>
    </tr>
    <tr id="person_name_for_mail">
        <th><?php echo $fieldnames['person_name_for_mail']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('person_name_for_mail',  array('div' => false, 'label' => false)); ?>
        <div class="comment">ログイン後の画面右上の名称表示やメール本文に使用されます。</div>
        <div class="comment">【注意】半角カタカナ及び、機種依存文字を使用するとメール本文で文字化けします。</div></td>
    </tr>
    <tr id="contact_address">
        <th><?php echo $fieldnames['contact_address']; ?></th>
        <td><?php echo $this->Form->input('contact_address', array('type' => 'textarea', 'div' => false, 'label' => false)); ?>
        <div class="comment">HOME画面右に表示される受付連絡先です。<br />
        1行目は連絡先のタイトルになります。</div></td>
    </tr>
    <tr id="mps_customer">
        <th><?php echo $fieldnames['mps_customer_id']; ?><span class="required_mark">*</span></th>
        <td><table>
              <tr><td valign="top">
                選択された顧客：<br/>
                <select id="cust_sel" name="cust_sel" multiple="multiple" size="9"></select>
              </td>
              <td>
                <br/><br/>
                <input type="button" name="btnRight" value=" → " onclick="onSelectObj(document.forms[0].cust_sel,document.forms[0].cust_all)"/>
                <br/><br/>
                <input type="button" name="btnLeft" value=" ← " onclick="onSelectObj(document.forms[0].cust_all,document.forms[0].cust_sel)"/>
              </td>
              <td valign="top">
                選択していない顧客：<br/>
                <select id="cust_all" name="cust_all" size="9" multiple="multiple" id="cust_all">
                   <?php foreach ($customer_organizations as $k => $v) {
                     echo '<option value="'.$k.'">'.$v.'</option>';
                   } ?>
                </select>
              </td></tr>
           </table><br/>
        <?php echo $this->Form->input('mps_customer_id', array('type' => 'text', 'style' => 'display:none', 'div' => false, 'label' => false)); ?>
        <div class="comment">管轄会社を選択してください</div>
        </td>
    </tr>
<?php echo $this->element('edit_common_area'); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
