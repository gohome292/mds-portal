<?php $id = $record['Information']['id'];
$this->Attachment->setData($record); ?>
<!-- MAIN --><div id="MAIN" role="main">
<div class="pad">
    <dl>
        <dt>登録日</dt>
        <dd><?php echo datetime_format($record['Information']['created']); ?></dd>
    </dl>
    <dl>
        <dt>件名</dt>
        <dd><?php echo h($record['Information']['title']); ?></dd>
    </dl>
    <dl>
        <dt>本文</dt>
        <dd class="body"><?php echo $this->richText->createLink(nl2br(h($record['Information']['content']))); ?></dd>
    </dl>
    <?php
    if ($this->Attachment->is('file1')
    || $this->Attachment->is('file2')
    || $this->Attachment->is('file3')
    || $this->Attachment->is('file4')
    || $this->Attachment->is('file5')):
    ?>
    <dl>
        <dt>添付ファイル</dt>
        <?php
        for ($j = 1; $j <= 5; $j++):
            if ($this->Attachment->is("file{$j}")) {
                echo '<dd>';
                $this->Attachment->link("file{$j}");
                $this->Attachment->size("file{$j}");
                echo '</dd>';
            }
        endfor; ?>
    </dl>
    <?php endif; ?>
</div>
<!-- /MAIN --></div>
<?php echo $this->Html->css('Information/view'); ?>
