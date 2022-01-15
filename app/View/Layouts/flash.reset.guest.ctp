<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja-JP" lang="ja-JP" dir="ltr">
<head>
<meta name="robots" content="noindex,nofollow">
<meta http-equiv="refresh" content="5;URL=<?php echo $url; ?>">
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=980" />
<meta name="author" content="リコージャパン株式会社" />
<meta name="copyright" content="Copyright(c) RICOH JAPAN Corporation." />
<title>パスワード初期化完了 / Managed Document Services&trade; Customer Portal site | RICOH JAPAN</title>
<?php echo $this->Html->meta('icon');
echo $this->Html->script('vendors/jquery'); ?>
<script type="text/javascript">
//<![CDATA[
var base = '<?php echo $this->base; ?>';
//]]>
</script>
<?php
echo $this->Html->script('guest/base');
echo $this->Html->css('guest/base');
?>
</head>
<body id="Logout">
<!-- CONTAINER --><div id="TOP">
<noscript><p>※当サイトではJavaScriptを使用しています。ブラウザ設定で「JavaScriptを有効にする」にして、正しく表示・機能する状態でご覧ください。</p></noscript>
<div id="HEADER" class="clear" role="banner">
    <dl class="clear">
        <dt><?php echo $this->Html->image(
            'guest/header/ricoh.gif',
            array(
                'width' => '109',
                'height' => '20',
                'alt' => 'RICOH',
            )
        ); ?></dt>
        <dd><?php echo $this->Html->image(
            'guest/header/logo.gif',
            array(
                'width' => '370',
                'height' => '16',
                'alt' =>
                    'Managed Document Services&trade; Customer Portal site',
            )
        ); ?></dd>
        <dd class="hide"><a href="#MAIN">ページ本文へジャンプ</a></dd>
    </dl>
</div>
<!-- MAIN --><div id="MAIN" role="main">
<div class="box">
    <h2>パスワード初期化完了</h2>
    <p>パスワードの初期化を完了しました。<br />
    パスワードをメールで送信しましたので、ログイン後にパスワードの変更をお願い致します。</p>
    <p>5秒後、自動的にログインページへ移動します。</p>
</div>
<!-- /MAIN --></div>
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