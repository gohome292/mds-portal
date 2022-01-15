<?php echo $this->Html->script('guest/nav'); ?>
<div id="NAV" class="clear" role="navigation">
  <ul>
    <li><a id="Nav-Home" href="<?php echo $this->base; ?>/information/index/">ホーム</a></li>
<?php
$counter=0;
//------------------------------------------------------------
// 報告書
if ($this->session->read('Auth.User.nav.documents')):
    $counter=$counter+1;
?>
    <li><a id="Nav-Report" href="<?php echo $this->base; ?>/documents/index/">報告書</a></li>
<?php endif;?>
<?php
//------------------------------------------------------------
// 機器管理情報
if ($this->session->read('Auth.User.nav.equipments')):
    $counter=$counter+1;
?>
    <li><a id="Nav-Equip" href="<?php echo $this->base; ?>/equipment/index/">機器管理情報</a></li>
<?php endif;?>
<?php
//------------------------------------------------------------
// ドライバー
if ($this->session->read('Auth.User.nav.drivers')):
    $counter=$counter+1;
?>
    <li><a id="Nav-Driver" href="<?php echo $this->base; ?>/drivers/index/">プリンタドライバ</a></li>
<?php endif;?>
<?php
//------------------------------------------------------------
// マニュアル
if ($this->session->read('Auth.User.nav.manuals')):
    $counter=$counter+1;
?>
    <li><a id="Nav-Manual" href="<?php echo $this->base; ?>/manuals/index/">マニュアル</a></li>
<?php endif;?>
<?php
//------------------------------------------------------------
// MACD申請
if ($this->session->read('Auth.User.nav.macd_workflows')):
    $counter=$counter+1;
?>
    <li><a id="Nav-MacdWorkflow" href="<?php echo $this->base; ?>/macd_workflows/index/">月次報告</a></li>
<?php endif;?>
    <li><a id="Nav-Pwchg" href="<?php echo $this->base; ?>/users/self_edit/">パスワード変更</a></li>
    <li><a href="<?php echo $this->base; ?>/users/logout/">ログアウト</a></li>
<?php
//------------------------------------------------------------
// 空白埋む
  for ($i = $counter+3; $i < 8; $i++):
?>
    <li><img src="<?php echo $this->base; ?>/img/guest/nav/null_off.png" width="114"></li>
<?php endfor;?>
  </ul>
</div>
