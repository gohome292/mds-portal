<?php
class InformationController extends AppController
{
    var $title = 'お知らせ';
    var $uses = array(
        'Information',
        'customer_organizations',
        'User',
    );
    var $components = array(
        'ActionCommon',
        'ActionIndex',
    );
    var $helpers = array(
        'Iggy.RichText',
        'Iggy.Attachment',
        'Mds',
    );
    var $layout = 'default.guest';
    
    function beforeFilter()
    {
        if ($this->request->action == 'index') {
            $this->title = 'ホーム';
            $this->set('body_id', 'Home');
        } else {
            $this->set('body_id', 'Info');
        }
        parent::beforeFilter();
        
        // お客様
        if ($this->Auth->user('group_id') == 3) {
            if (!$this->Session->check('Auth.User.access_information_rec')) {
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField(
                    'access_information',
                    date('Y-m-d H:i:s')
                );
                $this->Session->write(
                    'Auth.User.access_information_rec',
                    true
                );
            }
        }
    }
    
    function index()
    {
        if ($this->Auth->user('group_id') != 3) {
            if ($this->Auth->user('group_id') < 3) {
                $this->ActionCommon->setList(
                    'CustomerOrganization',
                    array(
                        'conditions' => array(
                            'level' => 1,
                        ),
                        'order' => array(
                            'sort' => 'ASC',
                        ),
                    )
                );
            } else {
                $this->ActionCommon->setList(
                    'CustomerOrganization',
                    array(
                        'conditions' => array(
                            'level' => 1,
                            'id' => explode('|',$this->Auth->user('mps_customer_id')),
                        ),
                        'order' => array(
                            'sort' => 'ASC',
                        ),
                    )
                );
            }
            if (isset($this->request->data['customer_organization']['id'])) {
                $this->_setCustId($this->request->data['customer_organization']['id']);
            } else {
                if ($this->Session->check('Auth.User.top_customer_organization_id')) {
                    $this->_setCustId($this->Session->read('Auth.User.top_customer_organization_id'));
                }
            }
       }
        $this->helpers[] = 'Iggy.New';
        // ------------------------------------------------------------
        // 新着お知らせ
        $params = array(
            'fields' => array(
                'Information.id',
                'Information.title',
                'Information.created',
            ),
            'conditions' => array(
                'Information.regular' => false,
                'Information.customer_organization_id' =>
                    $this->Auth->user('top_customer_organization_id'),
                'Information.level >=' => $this->Auth->user('level'),
            ),
            'order' => array(
                'Information.created' => 'DESC',
            ),
            'limit' => 20,
        );
        $this->set('records', $this->model->find('all', $params));
        
        // ------------------------------------------------------------
        // 常時表示お知らせ
        $params = array(
            'fields' => array(
                'Information.id',
                'Information.title',
                'Information.created',
            ),
            'conditions' => array(
                'Information.regular' => true,
                'Information.customer_organization_id' =>
                    $this->Auth->user('top_customer_organization_id'),
                'Information.level >=' => $this->Auth->user('level'),
            ),
            'order' => array(
                'Information.created' => 'DESC',
            ),
        );
        $this->set('regular_records', $this->model->find('all', $params));
    }
    
    // @param integer $id
    function view($id)
    {
        $params = array(
            'fields' => array(
                'Information.*',
            ),
            'conditions' => array(
                'Information.customer_organization_id' =>
                    $this->Auth->user('top_customer_organization_id'),
                'Information.level >=' => $this->Auth->user('level'),
                'Information.id' => $id,
            ),
        );
        $record = $this->model->find('first', $params);
        // @exception
        if (empty($record)) {
            //$this->ActionCommon->notFound($id);
            error404('InformationController.view');
        }
        $this->set(compact('record'));
    }
    
    function _setCustId($orgid = null) {
        if (empty($orgid)) {
            $this->Session->write(
                'customer_organizations',
                null
            );
            $this->Session->write(
               'Auth.User.top_customer_organization_id',
                0
            );
            $this->Session->write(
               'Auth.User.customer_organization_id',
                0
            );
            $this->Session->write(
                'Auth.User.nav',
                ''
            );
        } else {
            $this->Session->write(
                'customer_organizations',
                $this->CustomerOrganization->ChildrenAndSelf(
                    $orgid
                )
            );
            $this->Session->write(
                'Auth.User.level',
                1
            );
            $this->Session->write(
               'Auth.User.top_customer_organization_id',
                $orgid
            );
            $this->Session->write(
               'Auth.User.customer_organization_id',
                $orgid
            );
            // お客様提供サービスを取得
            $this->loadModel('CustomerNav');
            $nav = $this->CustomerNav->findById($orgid);
            if (empty($nav)) {
                $nav['CustomerNav'] = array('start_year_month' => date('Y/m'),
                    'documents' => false,'equipments' => false,'drivers' => false,'manuals' => false,'macd_workflows' => false);
            }
            $this->Session->write('Auth.User.nav', $nav['CustomerNav']);
        }
    }
}
