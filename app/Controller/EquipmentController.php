<?php
class EquipmentController extends AppController
{
    var $title = '機器管理情報';
    var $uses = array(
        'Equipment',
        'CustomerOrganization',
    );
    var $components = array(
        'ActionCommon',
        'MdsActionIndex',
        'Paginator',
        'Iggy.SearchRecall',
    );
    var $layout = 'default.guest';
    
    function beforeFilter()
    {
        $this->set('body_id', 'Equip');
        parent::beforeFilter();
        
        // お客様
        if ($this->Auth->user('group_id') == 3) {
            // @exception
            if (!$this->Session->read('Auth.User.nav.equipments')) die;
        }
    }
    
    function index()
    {
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Tree';
        
        if (!$this->Session->check('customer_organizations')) {
            $this->Session->write(
                'customer_organizations',
                $this->CustomerOrganization->ChildrenAndSelf(
                    $this->Auth->user('customer_organization_id')
                )
            );
        }
        $this->set(
            'customer_organizations',
            $this->Session->read('customer_organizations')
        );
        $this->request->data['customer_organization_id'] =
            $this->Auth->user('customer_organization_id');
    }
    
    function block_index()
    {
        // @exception
        if (!$this->RequestHandler->isAjax()) die;
        
        $this->helpers[] = 'Iggy.Attachment';
        
        if (empty($this->request->named['customer_organization_id'])) {
            $this->request->data[$this->modelClass]['customer_organization_id'] =
                $this->Auth->user('customer_organization_id');
        } else {
            $this->request->data[$this->modelClass]['customer_organization_id'] =
                $this->request->named['customer_organization_id'];
        }
        
        $this->CustomerOrganization->id =
            $this->request->data[$this->modelClass]['customer_organization_id'];
        $this->set(
            'customer_organization_name',
            $this->CustomerOrganization->field('name')
        );
        
        $option = array(
            "{$this->modelClass}.customer_organization_id" => 'CASE1',
        );
        $this->MdsActionIndex->run($option);
    }
}
