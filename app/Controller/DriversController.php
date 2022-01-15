<?php
class DriversController extends AppController
{
    var $title = 'プリンタードライバ一覧';
    var $uses = array(
        'Driver',
        'CustomerOrganization',
        'DriverManualType',
        'User',
    );
    var $components = array(
        'ActionCommon',
        'ActionIndex',
        'Paginator',
        'Iggy.SearchRecall',
    );
    var $layout = 'default.guest';
    public $paginate = array();
    // ドライバーマニュアル区分
    var $driver_manual_id = '1';
    
    function beforeFilter()
    {
        $this->set('body_id', 'Driver');
        parent::beforeFilter();

        // お客様
        if ($this->Auth->user('group_id') == 3) {
            if (!$this->Session->check('Auth.User.access_drivers_rec')) {
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField(
                    'access_drivers',
                    date('Y-m-d H:i:s')
                );
                $this->Session->write(
                    'Auth.User.access_drivers_rec',
                    true
                );
            }
        }
    }
    
    // @param integer $year_month ex) 201201
    function index($id = null)
    {
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Attachment';
        
        // 分類リスト取得
        $type_list_record = $this->DriverManualType->find('list', array(
                'conditions' => array('DriverManualType.driver_manual_id' => $this->driver_manual_id,
                                      'customer_organization_id' => $this->Auth->user('top_customer_organization_id')),
                'fields' => array('DriverManualType.id', 'DriverManualType.driver_manual_type')
        ));
        $this->set(
            'type_list',
            $type_list_record
        );
        if (empty($id)) {
            if (!empty($type_list_record)) {
                $id = key($type_list_record);
                $this->redirect(array('action' => 'index', $id));
            }
        }
        $this->request->data['driver_manual_type_id'] = $id;
            
        $this->paginate['conditions'] = array(
              'Driver.customer_organization_id' => $this->Auth->user('top_customer_organization_id'),
              'Driver.driver_manual_type_id' => $id,
            );

        $option = array();
        $this->ActionIndex->run($option);
    }
}

