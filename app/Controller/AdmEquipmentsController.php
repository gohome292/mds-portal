<?php
class AdmEquipmentsController extends AppController
{
    var $uses = array('Equipment');
    var $title = '機器管理情報';
    var $components = array(
        'ActionCommon',
        'MdsActionIndex',
        'ActionAdd',
        'ActionEdit',
        'ActionSave',
        'ActionRemove',
        'Paginator',
        'Iggy.SearchRecall',
    );
    var $helpers = array(
        'Iggy.Attachment',
    );
    
    // @param integer $id
    function index($id = null)
    {
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Menu';
        $this->helpers[] = 'Iggy.Tree';
        $this->ActionCommon->setMenu();
        $this->ActionCommon->setAcl(array('add', 'edit', 'remove'));
        
        $this->loadModel('CustomerOrganization');
        if (!$this->Session->check('adm.customer_organizations')) {
            if ($this->Auth->user('group_id') < 3) {
                $this->Session->write(
                    'adm.customer_organizations',
                    $this->CustomerOrganization->getTree());
            } else {
                $conditions = array(
                    'CustomerOrganization.top_parent_id' => explode('|',$this->Auth->user('mps_customer_id')),
                );
                $fields = array(
                    'CustomerOrganization.top_parent_id',
                );
                $this->Session->write(
                    'adm.customer_organizations',
                    $this->CustomerOrganization->getTree(compact('fields', 'conditions')));
            }
        }
        $this->loadModel('CustomerNav');
        $navs = $this->CustomerNav->find('list', array(
            'fields' => array('equipments'),
        ));
        $customer_organizations = $this->Session->read('adm.customer_organizations');
        $isDel = false;
        $cnt = count($customer_organizations);
        for ($i = 0; $i < $cnt; $i++) {
            $cust = $customer_organizations[$i]['CustomerOrganization'];
            if ($cust['level'] == 1) {
                if (empty($navs[$cust['id']])) {
                    $isDel = true;
                    unset($customer_organizations[$i]);
                } else {
                    $isDel = false;
                }
            } elseif ($isDel) {
                unset($customer_organizations[$i]);
            }
        }
        $customer_organizations = array_values($customer_organizations);
        $this->set(
            'customer_organizations',$customer_organizations);
        
        if ($id !== '0') {
            $this->request->data['customer_organization_id'] =
                $this->Session->read('Mds.customer_organization_id');
        }
    }
    
    function block_index()
    {
        // @exception
        if (!$this->RequestHandler->isAjax()) die;
        // @exception
        if (empty($this->request->named['customer_organization_id'])) {
            err('customer_organization_idが無い。');
            die;
        }
        
        $this->loadModel('CustomerOrganization');
        $this->set(
            'parent_records',
            $this->CustomerOrganization->ChildrenAndSelf(
                $this->request->named['customer_organization_id'],
                true,
                array('id')
            )
        );
        
        $this->request->data[$this->modelClass]['customer_organization_id'] =
            $this->request->named['customer_organization_id'];
        
        $option = array(
            "{$this->modelClass}.customer_organization_id" => 'CASE1',
        );
        $this->MdsActionIndex->run($option);
    }
    
    function add()
    {
        // @exception
        if (empty($this->request->named['customer_organization_id'])
        && empty($this->request->data[$this->modelClass]['customer_organization_id'])) {
            $this->flash(
                '報告書を登録したい対象の顧客組織をクリックして下さい。',
                "/{$this->request->controller}/index",
                Configure::read('App.pause')
            );
            return;
        }
        
        // 戻る組織を保存
        if (!empty($this->request->named['base_customer_organization_id'])) {
            $this->Session->write(
                'Mds.customer_organization_id',
                $this->request->named['base_customer_organization_id']
            );
        } elseif (!empty($this->request->named['customer_organization_id'])) {
            $this->Session->write(
                'Mds.customer_organization_id',
                $this->request->named['customer_organization_id']
            );
        }
        
        // 既存レコード確認
        if (!empty($this->request->named['customer_organization_id'])) {
            $params = array(
                'conditions' => array(
                    "{$this->modelClass}.customer_organization_id =" =>
                        $this->request->named['customer_organization_id'],
                ),
            );
            $count = $this->model->find('count', $params);
            if ($count > 0) {
                $record = $this->model->find('first', $params);
                $id = $record[$this->modelClass]['id'];
                $this->redirect(
                    "/{$this->request->controller}/edit/{$id}"
                );
                die;
            }
            $this->model->create(false);
        }
        
        $default = array($this->modelClass => array());
        
        // 初期値設定
        if (!empty($this->request->named['customer_organization_id'])) {
            $default[$this->modelClass]['customer_organization_id'] =  intval(
                $this->request->named['customer_organization_id']
            );
        }
        
        // 組織パス取得
        if (!empty(
            $this->request->data[$this->modelClass]['customer_organization_id']
        )) {
            $customer_organization_id =
                $this->request->data[$this->modelClass]['customer_organization_id'];
        } else {
            $customer_organization_id =
                $default[$this->modelClass]['customer_organization_id'];
        }
        $this->_setCustomerOrganizationPath($customer_organization_id);
        
        $this->ActionAdd->run(compact('default'));
    }
    
    // @param integer $id
    function edit($id = null)
    {
        if (empty($id) && !empty($this->request->data[$this->modelClass]['id'])) {
            $id = $this->request->data[$this->modelClass]['id'];
        }
        
        $this->ActionEdit->run($id, array('action' => 'index'));
        $this->_setCustomerOrganizationPath(
            $this->request->data[$this->modelClass]['customer_organization_id']
        );
    }
    
    // @param integer $id
    function remove($id)
    {
        $this->ActionRemove->run($id);
    }
    
    // @param integer $id
    // @return void
    function _setCustomerOrganizationPath($id)
    {
        $this->loadModel('CustomerOrganization');
        $this->set(
            'customer_organization_path',
            $this->CustomerOrganization->getpathstr($id)
        );
    }
}
