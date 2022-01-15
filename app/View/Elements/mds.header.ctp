<?php
echo '<span id="ricoh">'
   . $this->Html->image('guest/header/ricoh.jpg',array(
                'width' => '115',
                'alt' => 'RICOH',
            ))
   . '</span>'
   . '<span id="logo" style="vertical-align: 15px">'
   . $this->Html->image('guest/header/logo.gif')
   . '</span>'
   . $this->Html->css('elements/header.guest');
