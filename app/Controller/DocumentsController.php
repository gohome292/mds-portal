<?php
class DocumentsController extends AppController
{
    var $title = '報告書';
    var $uses = array(
        'Document',
        'CustomerOrganization',
        'User',
    );
    var $components = array(
        'ActionCommon',
        'MdsDateManager',
        'MdsActionIndex',
        'Paginator',
        'Iggy.SearchRecall',
    );
    var $layout = 'default.guest';
    
    function beforeFilter()
    {
        $this->set('body_id', 'Report');
        parent::beforeFilter();
        
        // お客様
        if ($this->Auth->user('group_id') == 3) {
            // @exception
            if (!$this->Session->read('Auth.User.nav.documents')) die;
            
            if (!$this->Session->check('Auth.User.access_documents_rec')) {
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField(
                    'access_documents',
                    date('Y-m-d H:i:s')
                );
                $this->Session->write(
                    'Auth.User.access_documents_rec',
                    true
                );
            }
        }
    }
    
    // @param integer $year_month ex) 201201
    function index($year_month = null)
    {
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Tree';
        
        $this->set(
            'year_months',
            $this->MdsDateManager->getOptions_YearMonth(
                $this->Auth->user('top_customer_organization_id')
            )
        );
        
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
        
        if (empty($this->request->data['year_month'])) {
            if (!empty($year_month)) {
                $this->request->data['year_month'] = $year_month;
            } else {
                //$this->request->data['year_month'] = date('Ym');
                $ym = strtotime('-1 month', strtotime(date('Y/m/01 00:00:00')));
                $this->request->data['year_month'] = date('Ym', $ym);
            }
        }
        $this->request->data['customer_organization_id'] =
            $this->Auth->user('customer_organization_id');
    }
    
    function block_index()
    {
        // @exception
        //if (!$this->RequestHandler->isAjax()) die;
        
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
        
        $this->request->data[$this->modelClass]['year_month'] =
            $this->request->named['year_month'];
        $this->request->data[$this->modelClass]['open_flag'] = array('0',);
        $option = array(
            "{$this->modelClass}.year_month" => '=',
            "{$this->modelClass}.open_flag" => '=',
            "{$this->modelClass}.customer_organization_id" => 'CASE1',
        );
        $this->MdsActionIndex->run($option);
    }
}
