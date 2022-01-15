<?php
class AdmDriversController extends AppController
{
    var $uses = array('Driver',
                      'DriverManualType',);
    var $title = 'プリンタドライバ';
    var $components = array(
        'ActionCommon',
        'ActionIndex',
        'ActionAdd',
        'ActionEdit',
        'ActionSave',
        'ActionRemove',
        'Paginator',
        'Iggy.SearchRecall',
    );
    public $paginate = array();
    var $helpers = array(
        'Iggy.Attachment',
    );
    
    // ドライバーマニュアル区分：１：プリンタドライバ
    var $driver_manual_id = '1';
    
    // @param integer $id
    function index($id = null)
    {
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Menu';
        $this->helpers[] = 'Iggy.Tree';
        $this->ActionCommon->setMenu();
        $this->ActionCommon->setAcl(array('add', 'edit', 'remove'));

        $this->loadModel('CustomerNav');
        $nav = $this->CustomerNav->find('list', array(
            'conditions' => array('CustomerNav.drivers' => '1'),
        ));
        if ($this->Auth->user('group_id') < 3) {
            $this->ActionCommon->setList(
                'CustomerOrganization',
                array(
                    'conditions' => array(
                        'level' => 1,
                        'id' => $nav,
                    ),
                    'order' => array(
                        'sort' => 'ASC',
                    ),
                )
            );
        } else {
            $ids = explode('|',$this->Auth->user('mps_customer_id'));
            foreach ($nav as $key => $value) {
                if (!in_array($value, $ids))
                    unset($nav[$key]);
            }
            $this->ActionCommon->setList(
                'CustomerOrganization',
                array(
                    'conditions' => array(
                        'level' => 1,
                        'id' => $nav,
                   ),
                    'order' => array(
                        'sort' => 'ASC',
                    ),
                )
            );
        }
        $customer_organizations = $this->viewVars['customer_organizations'];
        if (empty($id)) {
            $id = key($customer_organizations);
        }
        $this->request->data['customer_organization_id'] = $id;
        $this->request->data['driver_manual_id'] = $this->driver_manual_id;
        $this->Session->write('Mds.customer_organization_id',$id);
        // 分類リスト取得
        $type_list_record = $this->DriverManualType->find('list', array(
                'conditions' => array('DriverManualType.driver_manual_id' => $this->driver_manual_id,
                                      'customer_organization_id' => $id),
                'fields' => array('DriverManualType.id', 'DriverManualType.driver_manual_type')
        ));
        
        $this->set(
            'type_list',
            $type_list_record
        );
        
        if(!empty($this->request->named['driver_manual_type_id'])){
            $driver_manual_type_id = $this->request->named['driver_manual_type_id'];
        } else {
            $driver_manual_type_id = false;
        }
        $this->set(compact('driver_manual_type_id'));
        
    }
    
    function block_index()
    {
        $this->paginate['conditions'] = array(
              'Driver.customer_organization_id' => $this->request->named['customer_organization_id'],
              'Driver.driver_manual_type_id' => $this->request->named['type_list'],
            );
        
        $option = array();
        $this->ActionIndex->run($option);
    }
    
    function add()
    {
        if(!empty($this->request->named['customer_organization_id'])){
            $customer_organization_id = $this->request->named['customer_organization_id'];
        } else {
            $customer_organization_id = $this->request->data['Driver']['customer_organization_id'];
        }
        
        if(!empty($this->request->named['driver_manual_type_id'])){
            $driver_manual_type_id = $this->request->named['driver_manual_type_id'];
        } else {
            $driver_manual_type_id = $this->request->data['Driver']['driver_manual_type_id'];
        }

        // 分類リスト取得
        $type_list = $this->DriverManualType->find('list', array(
                'conditions' => array('DriverManualType.driver_manual_id' => $this->driver_manual_id,
                                      'customer_organization_id' => $customer_organization_id),
                'fields' => array('DriverManualType.id', 'DriverManualType.driver_manual_type')
        ));
        $this->set(compact('customer_organization_id', 'driver_manual_type_id', 'type_list'));

        $default = array($this->modelClass => array());
        $default[$this->modelClass]['customer_organization_id'] = $customer_organization_id;
        $default[$this->modelClass]['driver_manual_type_id'] = $driver_manual_type_id;
        
        if($this->ActionAdd->run(array('default' => $default,
                                       'flash' => false))){
            $message = '保存しました。';

            $this->flash(
                $message,
                "/adm_drivers/index/{$customer_organization_id}/driver_manual_type_id:{$driver_manual_type_id}/",
                Configure::read('App.pause')
            );
        } else {
            // 戻るURL設定
            $back_url = '/adm_drivers/index/' . $customer_organization_id . 
                        '/driver_manual_type_id:' . $driver_manual_type_id . '/';
            $this->set('backurl',$back_url);
        }
    }
    
    // @param integer $id
    function edit($id = null)
    {
    
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Menu';
        $this->ActionCommon->setMenu();
        $this->ActionCommon->setAcl(array('add', 'edit', 'remove'));

        if(empty($id) && !empty($this->request->data['Driver']['id']))
           $id = $this->request->data['Driver']['id'];
        if(!empty($this->request->data['Driver']['customer_organization_id'])) {
            $customer_organization_id = $this->request->data['Driver']['customer_organization_id'];
            $driver_manual_type_id = $this->request->data['Driver']['driver_manual_type_id'];
        } else {
            $record = $this->Driver->findById($id);
            $customer_organization_id = $record['Driver']['customer_organization_id'];
            $driver_manual_type_id = $record['Driver']['driver_manual_type_id'];
        }
        // 分類リスト取得
        $type_list = $this->DriverManualType->find('list', array(
                'conditions' => array('DriverManualType.driver_manual_id' => $this->driver_manual_id,
                                      'customer_organization_id' => $customer_organization_id),
                'fields' => array('DriverManualType.id', 'DriverManualType.driver_manual_type')
        ));
        
        // 戻るURL設定
        $back_url = '/adm_drivers/index/' . $customer_organization_id . 
                    '/driver_manual_type_id:' . $driver_manual_type_id . '/';

        $this->set(compact('type_list' ,'driver_manual_type_id'));

        if($this->ActionEdit->run($id, array('flash' => false))){
            $message = '保存しました。';
            $this->flash(
                $message,
                "/adm_drivers/index/{$customer_organization_id}/driver_manual_type_id:{$driver_manual_type_id}/",
                Configure::read('App.pause')
            );
        } else {
            $this->set('backurl',$back_url);
        }
    }
    
    // @param integer $id
    function remove($id)
    {
        $this->ActionRemove->run($id);
    }
    
}
