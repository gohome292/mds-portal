<?php
class CustomerOrganizationsController extends AppController
{
    var $title = '顧客組織';
    var $uses = array(
        'CustomerOrganization',
        'CustomerNav',
    );
    var $components = array(
        'ActionCommon',
        'ActionAdd',
        'ActionEdit',
        'ActionSave',
        'ActionRemove',
    );
    var $helpers = array(
        'Iggy.Tree',
    );
    
    function index()
    {
        $this->helpers[] = 'Iggy.Menu';
        $this->ActionCommon->setMenu();
        $this->ActionCommon->setAcl(array('add', 'edit', 'remove'));
        $this->set('records', $this->model->getTree());
    }
    
    // @param integer $id
    function refer($id = null)
    {
        //l($this->model->getTree(array('path' => true)));
        if (empty($this->request->named['top'])) {
            $this->set('records', $this->model->getTree(array('path' => true)));
        } else {
            $orgs = explode(">", $this->request->named['top']);
            $cust = $this->CustomerOrganization->find('list', array(
                'fields' => array('id'),
                'conditions' => array(
                    'CustomerOrganization.name like ' => '%'.trim($orgs[0]).'%',
                    'CustomerOrganization.parent_id is null',
                ),)
            );
            if (empty($cust)) {
                $this->set('records', $this->model->getTree(array('path' => true)));
            } else {
                $this->set('records', $this->model->getTree(array('path' => true, 
                  'conditions' => array('CustomerOrganization.top_parent_id' => $cust))));
            }
        }
        $this->set(compact('id'));
    }
    
    function add()
    {
        if (!empty($this->request->data[$this->modelClass]['parent_id'])) {
            $this->model->id = $this->request->data[$this->modelClass]['parent_id'];
            $parent_level = $this->model->field('level');
            // @exception
            if ($parent_level >= 5) {
                $this->flash(
                    '階層は5階層までしか作成できません。',
                    "/{$this->request->controller}/index",
                    Configure::read('App.pause')
                );
                return;
            }
        }
        timeout();
        $this->ActionAdd->run(array('action' => 'add'));
    }
    
    // @param integer $id
    function edit($id)
    {
        if (!empty($this->request->data[$this->modelClass]['parent_id'])) {
            $this->model->id = $this->request->data[$this->modelClass]['parent_id'];
            $parent_level = $this->model->field('level');
            // @exception
            if ($parent_level >= 5) {
                $this->flash(
                    '階層は5階層までしか作成できません。',
                    "/{$this->request->controller}/index",
                    Configure::read('App.pause')
                );
                return;
            }
        }
        timeout();
        $this->ActionEdit->run($id);
        // フォームからポストされたデータがあるかどうか
        if (!empty($_POST)) {
            if (empty($this->request->data[$this->modelClass]['parent_id'])) {
                $data['CustomerNav'] = $this->request->data['CustomerNav'];
                if (!$data['CustomerNav']['id'])
                    $data['CustomerNav']['id'] = $id;
                $this->CustomerNav->save($data);
            }
        } else {
            if (empty($this->request->data[$this->modelClass]['parent_id'])) {
                $nav = $this->CustomerNav->findById($id);
                if (empty($nav)) {
                    $nav['CustomerNav'] = array('start_year_month' => date('Y/m'),
                        'documents' => false,'equipments' => false,'drivers' => false,'manuals' => false,'macd_workflows' => false);
                }
                $this->request->data['CustomerNav'] = $nav['CustomerNav'];
            }
        }
    }
    
    // @param integer $id
    function remove($id)
    {
        $this->ActionRemove->run($id);
        $this->CustomerNav->delete($id);
    }
}
