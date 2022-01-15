<?php
class AutoReadHelper extends AppHelper
{
    var $helpers = array(
        'Html',
        'Js',
    );
    
    // @return void
    function run()
    {
        $path = $this->request->controller . '/' . $this->request->action;
        $filename = WWW_ROOT . '%s' . DS . $this->request->controller . DS
                  . $this->request->action . '.%1$s';
        if (is_readable(sprintf($filename, 'js'))) {
            echo $this->Html->script($path);
        }
        if (is_readable(sprintf($filename, 'css'))) {
            echo $this->Html->css($path);
        }
    }
}
