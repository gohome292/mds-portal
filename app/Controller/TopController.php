<?php
class TopController extends AppController
{
    var $title = 'トップ';
    var $uses = array();
    var $components = array(
        'ActionCommon',
    );
    
    function beforeFilter()
    {
        parent::beforeFilter();
        if ($this->Auth->user('id')) $this->Auth->allow('*');
    }
    
    function index()
    {
        $this->ActionCommon->setMenu();
    }
}
