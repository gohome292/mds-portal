<?php
App::uses('AppHelper', 'View/Helper');
class MenuHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'Form',
    );
    
    function back()
    {
        $backurl = $this->_View->getVar('backurl');
        if (empty($backurl)) return;
        echo '<li>' .  $this->Html->link(
            $this->Html->image('menu/back.gif'),
            $backurl,
            array('escape' => false)
        ) . '</li>';
    }
    
    function view($name, $id, $options = array())
    {
        $view = $this->_View;
        $acl = $view->getVar('acl');
        $check_actions = array('view', 'admin_view');
        $action = NULL;
        foreach ($check_actions as $check_action) {
            if (!empty($acl[$check_action])) {
                $action = $check_action;
                break;
            }
        }
        if (empty($action)) {
            echo h($name);
            return;
        }
        echo $this->Html->link(
            $name,
            "/{$this->request->controller}/{$action}/{$id}",
            $options
        );
    }
    
    function add()
    {
        $acl = $this->_View->viewVars['acl'];
        $check_actions = array('add', 'admin_add');
        $action = NULL;
        foreach ($check_actions as $check_action) {
            if (!empty($acl[$check_action])) {
                $action = $check_action;
                break;
            }
        }
        if (empty($action)) return;
        echo '<li id="menu_add">' . $this->Html->link(
            $this->Html->image('menu/add.gif'),
            "/{$this->request->controller}/{$action}",
            array('escape' => false)
        ) . '</li>';
    }
    
    // @param integer $id
    function edit($id)
    {
        $acl = $this->_View->viewVars['acl'];
        $check_actions = array('edit', 'admin_edit');
        $action = NULL;
        foreach ($check_actions as $check_action) {
            if (!empty($acl[$check_action])) {
                $action = $check_action;
                break;
            }
        }
        if (empty($action)) return;
        echo '<li id="menu_edit">' . $this->Html->link(
            $this->Html->image('menu/edit.gif'),
            "/{$this->request->controller}/{$action}/{$id}",
            array('escape' => false)
        ) . '</li>';
    }
    
    // @param integer $id
    function remove($id)
    {
        if (isset($this->_View->viewVars['acl']))
           $acl = $this->_View->viewVars['acl'];
        else
           $acl = array();
        $check_actions = array('remove', 'admin_remove');
        $action = NULL;
        foreach ($check_actions as $check_action) {
            if (!empty($acl[$check_action])) {
                $action = $check_action;
                break;
            }
        }
        if (empty($action)) return;
        if (Configure::read('App.simple_message')) {
            $message = '削除して宜しいですか？';
        } else {
            $message = "ID:{$id}を削除して宜しいですか？";
        }
        echo '<li id="menu_remove">' . $this->Html->link(
            $this->Html->image('menu/remove.gif'),
            "/{$this->request->controller}/{$action}/{$id}",
            array('escape' => false),
            $message
        ) . '</li>';
    }
    
    function edit_title()
    {
        $acl = $this->_View->viewVars['acl'];
        $check_actions = array('edit', 'admin_edit');
        $action = NULL;
        foreach ($check_actions as $check_action) {
            if (!empty($acl[$check_action])) {
                $action = $check_action;
                break;
            }
        }
        if (empty($action)) return;
        echo '<th class="edit">編集</th>';
    }
    
    function remove_title()
    {
        $acl = $this->_View->viewVars['acl'];
        $check_actions = array('remove', 'admin_remove');
        $action = NULL;
        foreach ($check_actions as $check_action) {
            if (!empty($acl[$check_action])) {
                $action = $check_action;
                break;
            }
        }
        if (empty($action)) return;
        echo '<th class="remove">削除</th>';
    }
    
    // @param integer $id
    function edit_link($id)
    {
        $acl = $this->_View->viewVars['acl'];
        $check_actions = array('edit', 'admin_edit');
        $action = NULL;
        foreach ($check_actions as $check_action) {
            if (!empty($acl[$check_action])) {
                $action = $check_action;
                break;
            }
        }
        if (empty($action)) return;
        echo '<td class="edit center">' .  $this->Html->link(
            '編集',
            "/{$this->request->controller}/{$action}/{$id}"
        ) . '</td>';
    }
    
    // @param integer $id
    function remove_link($id)
    {
        $acl = $this->_View->viewVars['acl'];
        $check_actions = array('remove', 'admin_remove');
        $action = NULL;
        foreach ($check_actions as $check_action) {
            if (!empty($acl[$check_action])) {
                $action = $check_action;
                break;
            }
        }
        if (empty($action)) return;
        if (Configure::read('App.simple_message')) {
            $message = '削除して宜しいですか？';
        } else {
            $message = "ID:{$id}を削除して宜しいですか？";
        }
        echo '<td class="remove center">' .  $this->Html->link(
            '削除',
            "/{$this->request->controller}/{$action}/{$id}",
            array(),
            $message
        ) . '</td>';
    }
}
