<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja-JP" lang="ja-JP" dir="ltr">
<head>
<meta name="robots" content="noindex,nofollow">
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=980" />
<meta name="author" content="リコージャパン株式会社" />
<meta name="copyright" content="Copyright(c) RICOH JAPAN Corporation." />
<title><?php echo $title_for_layout; ?></title>
<?php echo $this->Html->meta('icon');
echo $this->element('library.guest'); ?>
</head>
<body id="<?php echo $body_id ?>">
<!-- CONTAINER --><div id="TOP">
<noscript><p>※当サイトではJavaScriptを使用しています。ブラウザ設定で「JavaScriptを有効にする」にして、正しく表示・機能する状態でご覧ください。</p></noscript>
<div id="HEADER" class="clear" role="banner">
    <dl class="clear">
        <dt><?php echo $this->Html->image(
            'guest/header/ricoh.jpg',
            array(
                'height' => '40px',
                'alt' => 'RICOH',
            )
        ); ?></dt>
        <dd><?php echo $this->Html->image(
            'guest/header/logo.gif',
            array(
                'width' => '370',
                'alt' =>
                    'Managed Document Services&trade; Customer Portal site',
            )
        ); ?></dd>
        <dd class="hide"><a href="#MAIN">ページ本文へジャンプ</a></dd>
    </dl>
    <!-- ユーザー名表示 -->
    <H1>ようこそ<i>
    <?php
      if (empty($this->session->read('Auth.User.person_name_for_mail'))) {
        echo h($this->session->read('Auth.User.company_name_for_mail')) . '　' . h($this->session->read('Auth.User.name')); 
      } else {
        echo h($this->session->read('Auth.User.company_name_for_mail')) . '　' . h($this->session->read('Auth.User.person_name_for_mail')); 
      }?>
    </i>様</H1>
</div>
<?php echo $content_for_layout; ?>
<!-- /CONTAINER --><div id="MOVE"></div></div>
<div id="FOOTER" role="contentinfo"><p><?php echo $this->Html->image(
            'guest/footer/copyright.gif',
            array(
                'width' => '292',
                'height' => '10',
                'alt' =>
                    'Copyright&copy; '
                  . 'RICOH JAPAN Corporation. All Rights Reserved.',
            )
        ); ?></p></div>
</body>
</html>