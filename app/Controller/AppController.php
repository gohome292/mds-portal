<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    var $title;
    var $uses = array(
        'Menu',
    );
    public $components = array(
        'Acl',
        'Auth' => array(
            'authorize' => array(
                'Actions' => array('actionPath' => 'Controller')
            )
        ),
        'Session',
        'RequestHandler',
    );
    public $helpers = array('Html', 'Form', 'Session', 'Iggy.AutoRead');

    var $breadcrumbs;
    var $auto_breadcrumbs = true;
    function beforeFilter()
    {
        if ($this->modelClass) $this->model = $this->{$this->modelClass};
        if (isset($this->Auth)) {
            $this->Auth->authorize = 'Actions';
            $this->Auth->actionPath = 'Controller/';
            $this->Auth->allowedActions = array();
        }
        _default($this->title, '');
        $this->breadcrumbs = $this->title;
        $this->title =  "{$this->title} / " . Configure::read('App.name');
    }
    
    function beforeRender()
    {
        $this->set('title_for_layout', $this->title);
        if ($this->auto_breadcrumbs) {
            $action = str_replace('admin_', '', $this->request->action);
            $actions = fgetyml('actions');
            if (isset($actions[$action])) {
                $this->breadcrumbs .= ' > ' . $actions[$action];
            }
        }
        $this->set('breadcrumbs', $this->breadcrumbs);
    }
    
    function afterFilter()
    {
        $this->Session->write(
            "Past.{$this->request->controller}.action",
            $this->request->action
        );
    }
}
