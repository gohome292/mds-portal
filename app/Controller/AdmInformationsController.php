<?php
class AdmInformationsController extends AppController
{
    var $uses = array('Information');
    var $title = 'お知らせ';
    var $components = array(
        'ActionCommon',
        'ActionIndex',
        'ActionView',
        'ActionAdd',
        'ActionEdit',
        'ActionSave',
        'ActionRemove',
        'MdsOptionsManager',
        'Paginator',
        'Iggy.SearchRecall',
    );
    var $helpers = array(
        'Iggy.Tab',
        'Iggy.Attachment',
    );
    public $paginate = array();

    function index()
    {
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
            $cids = explode('|',$this->Auth->user('mps_customer_id'));
            $this->ActionCommon->setList(
                'CustomerOrganization',
                array(
                    'conditions' => array(
                        'level' => 1,
                        'id' => $cids,
                    ),
                    'order' => array(
                        'sort' => 'ASC',
                    ),
                )
            );
            $this->paginate['conditions'] = array(
              'customer_organization_id' => $cids,
            );
        }
        $option = array(
            'customer_organization_id' => '=',
            'level' => '<=',
            'regular' => '=',
            'title' => 'LIKE',
            'content' => 'LIKE',
        );
        $this->MdsOptionsManager->LevelIndex();
        $this->MdsOptionsManager->RegularIndex();
        
        $this->ActionIndex->run($option);
    }
    
    // @param integer $id
    function view($id)
    {
        $this->helpers[] = 'Iggy.RichText';
        $this->MdsOptionsManager->LevelIndex();
        $this->MdsOptionsManager->RegularIndex();
        $this->ActionView->run($id);
    }
    
    function add()
    {
        $this->_ready_edit();
        
        $this->ActionAdd->run();
    }
    
    // @param integer $id
    function edit($id = null)
    {
        if (empty($id) && !empty($this->request->data[$this->modelClass]['id'])) {
            $id = $this->request->data[$this->modelClass]['id'];
        }
        
        $this->_ready_edit();
        
        $this->ActionEdit->run($id);
    }
    
    // @param integer $id
    function remove($id)
    {
        $this->ActionRemove->run($id);
    }
    
    function _ready_edit()
    {
        if ($this->Auth->user('group_id') < 3) {
            $this->ActionCommon->setList(
                'CustomerOrganization',
                array(
                    'conditions' => array(
                        'parent_id' => null,
                    ),
                    'order' => array(
                        'sort' => 'ASC',
                    ),
                )
            );
        } else {
            $cids = explode('|',$this->Auth->user('mps_customer_id'));
            $this->ActionCommon->setList(
                'CustomerOrganization',
                array(
                    'conditions' => array(
                        'parent_id' => null,
                        'id' => $cids,
                    ),
                    'order' => array(
                        'sort' => 'ASC',
                    ),
                )
            );
        }
        $this->MdsOptionsManager->LevelIndex();
        $this->MdsOptionsManager->RegularIndex();
        
        $this->loadModel('Attachment');
        $this->set('attachments', $this->Attachment->getOptions());
    }
}
