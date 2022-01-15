<?php echo $this->element('mds.nav'); ?>
<!-- MAIN --><div id="MAIN" class="clear" role="main">
<div id="MENU" role="complementary">
    <?php if ($this->session->read('Auth.User.group_id') != 3) {
      echo $this->Form->create('Information', array('url' => 'index'));
      echo $this->Form->input(
          'customer_organization.id',
          array(
              'type' => 'select',
              'options' => $customer_organizations,
              'empty'   => '選択してください',
              'selected' => $this->session->read('Auth.User.top_customer_organization_id'),
              'label' => false,    // labelを出力しない
          )
      );
      echo $this->Form->end();
      if ($this->session->read('Auth.User.group_id') != 5) {
        echo $this->Form->button('編集', 
           array(
             'id' => 'edit_btn',
             'type' => 'button',
           )
        );
      }
    } elseif ($this->session->read('Auth.User.contact_address')) {
      echo $this->mds->format_contact_address($this->session->read('Auth.User.contact_address'));
    }
    ?>
    <p><?php echo $this->Html->image(
            'guest/main/catch.jpg',
            array(
                'width' => '150',
                'alt' => 'Managed Document Services&trade; MPS and Bewyond',
            )
        ); ?></p>
    <p><a href="http://www.ricoh.co.jp/" target="_blank" class="exit">株式会社リコー
</a></p>
</div>
<div id="CONTENT">
    <h2>新着お知らせ</h2>
    <div class="info">
        <!-- 新着表示 -->
        <?php
        $count = 0;
        foreach ($records as $record):
        $id = $record['Information']['id'];
        $count++;
        if ($count > Configure::read('Mds.Information.show_count.new')): ?>        <div class="archive">
        <?php endif; ?>
        <dl>
            <dt<?php if ($this->new->is($record['Information']['created'], 7)) echo ' class="new"'; ?>><?php echo datetime_format($record['Information']['created']); ?></dt>
            <dd><?php echo $this->Html->link(
                $record['Information']['title'],
                "/{$this->request->controller}/view/{$id}/",
                array('target' => '_blank')
            ); ?></dd>
        </dl>
        <?php
        if ($count > Configure::read('Mds.Information.show_count.new')): ?>        </div><!-- .archive -->
        <?php endif;
        endforeach; ?>
    <?php if ($count > Configure::read('Mds.Information.show_count.new')): ?>
    <div class="switch"><span>もっと見る</span></div>
    <?php endif; ?>
    <?php if ($count == 0): ?>
    <br />
    <?php endif; ?>
    </div><!-- .info -->
    <h2>お役立ち情報</h2>
    <div class="info">
        <!-- 常時表示 -->
        <?php
        $count = 0;
        foreach ($regular_records as $record):
        $id = $record['Information']['id'];
        $count++;
        if ($count > Configure::read('Mds.Information.show_count.regular')): ?>
        <div class="archive">
        <?php endif; ?>
        <dl>
            <dt<?php if ($this->new->is($record['Information']['created'], 7)) echo ' class="new"'; ?>><?php echo datetime_format($record['Information']['created']); ?></dt>
            <dd><?php echo $this->Html->link(
                $record['Information']['title'],
                "/{$this->request->controller}/view/{$id}/",
                array('target' => '_blank')
            ); ?></dd>
        </dl>
        <?php if ($count > Configure::read('Mds.Information.show_count.regular')): ?>
        </div><!-- .archive -->
        <?php endif;
        endforeach; ?>
    <?php if ($count > Configure::read('Mds.Information.show_count.regular')): ?>
    <div class="switch"><span>もっと見る</span></div>
    <?php endif; ?>
    </div><!-- .info -->
</div><!-- #CONTENT -->
<!-- /MAIN --></div>
