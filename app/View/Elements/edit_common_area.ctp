<?php if (!isset($colspan)) $colspan = '2'; ?>
    <tr>
        <td colspan="<?php echo $colspan; ?>"><span class="comment"><span class="required_mark">*</span>必須項目<span></td>
    </tr>
    <tr>
        <td colspan="<?php echo $colspan; ?>" class="right"><?php echo $this->Form->submit('保　存', array('class' => 'save'));/* echo $this->Form->button('キャンセル', array('type' => 'reset'));*/ ?></td>
    </tr>
