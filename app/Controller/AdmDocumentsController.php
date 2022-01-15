<?php
class AdmDocumentsController extends AppController
{
    var $uses = array(
       'Document',
       'CustomerOrganization',);

    var $title = '報告書';
    var $components = array(
        'ActionCommon',
        'MdsActionIndex',
        'ActionAdd',
        'ActionEdit',
        'ActionSave',
        'ActionRemove',
        'MdsDateManager',
        'Paginator',
        'Iggy.SearchRecall',
    );
    public $helpers = array(
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
        
        $this->set(
            'year_months',
            $this->MdsDateManager->getOptions_YearMonth()
        );
        $customer_organizations = array();
        if ($this->Auth->user('group_id') < 3) {
            $customer_organizations =
                $this->CustomerOrganization->getTree();
        } else {
            $conditions = array(
                'CustomerOrganization.top_parent_id' => explode('|',$this->Auth->user('mps_customer_id')),
            );
            $fields = array(
                'CustomerOrganization.top_parent_id',
            );
            $customer_organizations =
                $this->CustomerOrganization->getTree(compact('fields', 'conditions'));
        }
        $this->Session->write(
            'adm.customer_organizations',$customer_organizations);
        $this->set(
            'customer_organizations',$customer_organizations);
        $yyyy = date('Y');
        $AccesslogYears = array();
        while ($yyyy>=2018) {
            $basename  = 'accessLog' . $yyyy . ".csv";
            if (is_readable(DOWNLOADS . $basename)) {
                $AccesslogYears[$yyyy] = $yyyy.'年';
            }
            $yyyy = $yyyy -1;
        }
        $AccesslogYYYYId = date('Y');
        $this->set('AccesslogYears', $AccesslogYears);
        $this->set('AccesslogYYYYId', $AccesslogYYYYId);
        if (!empty($this->request->named['AccesslogYYYYId'])) {
            if (!$this->_download($this->request->named['AccesslogYYYYId'])) {
                $this->redirect(array('action' => 'index'));
            }
        }
        if ($id !== '0') {
            $this->request->data['customer_organization_id'] =
                $this->Session->read('Mds.customer_organization_id');
        }
        if ($this->Session->check('Mds.year_month')) {
            $this->request->data['year_month'] = $this->Session->read('Mds.year_month');
        } else {
            $ym = strtotime('-1 month', strtotime(date('Y/m/01 00:00:00')));
            $this->request->data['year_month'] = date('Ym', $ym);
        }
    }
    
    function block_index()
    {
        // @exception
        if (!$this->RequestHandler->isAjax()) die;
        // @exception
        if (empty($this->request->named['year_month'])
           || empty($this->request->named['customer_organization_id'])) {
            die;
        }
        
        $this->set(
            'parent_records',
            $this->CustomerOrganization->ChildrenAndSelf(
                $this->request->named['customer_organization_id'],
                true,
                array('id')
            )
        );
        
        $this->request->data[$this->modelClass]['year_month'] =
            $this->request->named['year_month'];
        $this->request->data[$this->modelClass]['customer_organization_id'] =
            $this->request->named['customer_organization_id'];
        
        $option = array(
            "{$this->modelClass}.year_month" => '=',
            "{$this->modelClass}.customer_organization_id" => 'CASE1',
        );
        $this->MdsActionIndex->run($option);
    }
    
    function add()
    {
        $count = 0;
        // 戻る年月を保存
        if (!empty($this->request->named['year_month'])) {
            $this->Session->write(
                'Mds.year_month',
                $this->request->named['year_month']
            );
        }
        if (!empty($this->request->data[$this->modelClass]['year_month'])) {
            $this->Session->write(
                'Mds.year_month',
                $this->request->data[$this->modelClass]['year_month']
            );
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
        $customer_organization_id = 0;
        if (!empty($this->request->named['customer_organization_id'])) {
            $customer_organization_id = $this->request->named['customer_organization_id'];
            // 既存レコード確認
            $params = array(
                'conditions' => array(
                    "{$this->modelClass}.year_month =" =>
                        $this->request->named['year_month'],
                    "{$this->modelClass}.customer_organization_id =" =>
                        $customer_organization_id,
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
            $this->Session->write(
                'Mds.customer_organization_id',
                $customer_organization_id
            );
            $this->model->create(false);
        } else {
            if (empty($_POST)) {
	            if ($this->Auth->user('group_id') > 2) {
	                $this->flash(
	                    '報告書を登録したい対象の顧客組織をクリックして下さい。',
	                    "/{$this->request->controller}/index",
	                    Configure::read('App.pause')
	                );
	                return;
	            }
	            $this->auto_breadcrumbs = false;
	            $this->breadcrumbs .= ' > 直接登録';
	        }
            if(!empty($this->request->data[$this->modelClass]['customer_organization_id'])) {
                $customer_organization_id = $this->request->data[$this->modelClass]['customer_organization_id'];
            } elseif(!empty($this->request->data[$this->modelClass]['path'])) {
                $orgs = explode(">", $this->request->data[$this->modelClass]['path']);
                $cust = $this->CustomerOrganization->find('first', array(
                    'conditions' => array(
                        'CustomerOrganization.name' => trim($orgs[0]),
                        'CustomerOrganization.parent_id is null',
                    ),)
                );
                if(!empty($cust)){
                    $top_id = $cust['CustomerOrganization']['id'];
                    $parent_id = $cust['CustomerOrganization']['id'];
                    for ($i = 1; $i < count($orgs); $i++) {
                        $cust = $this->CustomerOrganization->find('first', array(
                            'conditions' => array(
                                'CustomerOrganization.name' => trim($orgs[$i]),
                                'CustomerOrganization.top_parent_id' => $top_id,
                                'CustomerOrganization.parent_id' => $parent_id,
                            ),)
                        );
                        if(empty($cust))
                            break;
                        $parent_id = $cust['CustomerOrganization']['id'];
                    }
                    if(!empty($cust)) {
                        $this->request->data[$this->modelClass]['customer_organization_id'] = $cust['CustomerOrganization']['id'];
                        $customer_organization_id = $this->request->data[$this->modelClass]['customer_organization_id'];
                    }
                }
            }
            if (!empty($_POST)) {
                if (empty($customer_organization_id)) {
                    $this->set('customer_error', '顧客組織を参照ボタンで選択してください。');
                } else {
                    // 既存レコード確認
                    $params = array(
                        'conditions' => array(
                            "{$this->modelClass}.year_month =" =>
                                $this->request->data[$this->modelClass]['year_month'],
                            "{$this->modelClass}.customer_organization_id =" =>
                                $customer_organization_id,
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
                    $this->Session->write(
                        'Mds.customer_organization_id',
                        $customer_organization_id
                    );
                    $this->model->create(false);
                }
            }
        }
        $default = array($this->modelClass => array());
        // 初期値設定
        if (!empty($this->request->named['year_month'])) {
            $default[$this->modelClass]['year_month'] =  intval(
                $this->request->named['year_month']
            );
        }
        if (!empty($this->request->data[$this->modelClass]['year_month'])) {
            $default[$this->modelClass]['year_month'] =  intval(
                $this->request->data[$this->modelClass]['year_month']
            );
        }
        
        if (!empty($this->request->named['customer_organization_id'])) {
            $default[$this->modelClass]['customer_organization_id'] =  intval(
                $this->request->named['customer_organization_id']
            );
        }
        
        $this->_setCustomerOrganizationPath($customer_organization_id);
        if (!empty($_POST)) {
           if ($this->request->data[$this->modelClass]['open_flag'] == '1') {
               if ($this->_checkMailExists($customer_organization_id, $this->request->data[$this->modelClass]['year_month'])>0)
                   $this->request->data[$this->modelClass]['open_flag'] = '2';
           }
           $this->_writeLog('新規', null);
        }
        if (!$this->ActionAdd->run(compact('default'))) {
            return false;
        }
    }
    
    // @param integer $id
    function edit($id = null)
    {
        if (empty($id) && !empty($this->request->data[$this->modelClass]['id'])) {
            $id = $this->request->data[$this->modelClass]['id'];
        }
        
        if (!empty($_POST)) {
           $this->_writeLog('更新', $id);
        }
        $this->ActionEdit->run($id, array('action' => 'index'));
        $this->_setCustomerOrganizationPath(
            $this->request->data[$this->modelClass]['customer_organization_id']
        );
    }
    
    // @param integer $id
    function remove($id)
    {
        $this->_writeLog('削除', $id);
        $this->ActionRemove->run($id);
    }
    
    // @param integer $id
    // @return void
    function _setCustomerOrganizationPath($id)
    {
        $this->set(
            'customer_organization_path',
            $this->CustomerOrganization->getpathstr($id)
        );
    }
    
    function _checkMailExists($cid, $yyyymm)
    {
        $document_check = 1;
        if (date('Ym') == $yyyymm) {
            $document_check = 2;
        }
        $c = $this->CustomerOrganization->findById($cid);
        $this->loadModel('MailHistory');
        $count = $this->MailHistory->find('count', array(
              'conditions' => array(
                    'MailHistory.customer_organization_id' => $c['CustomerOrganization']['top_parent_id'],
                    'MailHistory.document_check' => array($document_check,'0'),
                    'MailHistory.status' => 0,
                    )
                ));
        return $count;
    }
    
    function _writeLog($act, $id)
    {
        $now = date('Y-m-d H:i:s'); 
        $basename  = 'accessLog' . substr($now, 0, 4) . ".csv";
        $isExists = false;
        if (is_writable(DOWNLOADS . $basename)) {
            $isExists = true;
        }
        $handle = fopen(DOWNLOADS . $basename, 'a');
        if (!$isExists) {
            $titles = array(
                '日時',
                '顧客名',
                'ログインID',
                '氏名',
                '画面名',
                '操作の情報',
                '年月',
                '顧客情報',
                'メールで公開',
                '添付1',
                '添付2',
                '添付3',
                '添付4',
                '添付5',
            );
            fputcsv($handle, mbo($titles));
            unset($titles);
        }
        if ($act == '削除') {
            $doc = $this->model->find('first', array(
               'conditions' => array('Document.id' => $id)
            ));
            $ym = $doc['Document']['year_month'];
            $path = $this->CustomerOrganization->getpathstr($doc['CustomerOrganization']['id']);
            $openFlag = $doc['Document']['open_flag'];
            $fname = array('','','','','');
            foreach ($doc['Attachment'] as $att) {
                $i = substr($att['identifier'],4);
                $fname[$i-1] = $att['alternative'] . '.' . $att['extension'];
            }
        } else {
            if (empty($this->request->data[$this->modelClass]['customer_organization_id'])) {
                fclose($handle);
                return;
            }
            $ym = $this->request->data[$this->modelClass]['year_month'];
            if (isset($this->viewVars['customer_organization_path'])) {
                $path = $this->viewVars['customer_organization_path'];
            } else {
                $path = $this->CustomerOrganization->getpathstr(
                  $this->request->data[$this->modelClass]['customer_organization_id']);
            }
            $openFlag = $this->request->data[$this->modelClass]['open_flag'];
            $fname = $this->_getFileName($this->request->data['Attachment']);
        }
        if ($openFlag == '1') {
            $openFlag = '未公開';
        } else if ($openFlag == '2') {
            $openFlag = '公開待';
        } else {
            $openFlag = '公開済';
        }
        $custName = explode(">", $path);
        $record = array(
                    $now,
                    $custName[0],
                    $this->Auth->user('username'),
                    $this->Auth->user('name'),
                    '報告書',
                    $act,
                    $ym,
                    $path,
                    $openFlag,
                    $fname[0],
                    $fname[1],
                    $fname[2],
                    $fname[3],
                    $fname[4],
                );
        fputcsv($handle, mbo($record));
        unset($record);
        fclose($handle);
    }

    function _getFileName($attach)
    {
        $this->loadModel('Attachment');
        for ($i = 0; $i < count($attach); $i++) {
            if (isset($attach[$i]['id'])) {
                $a = $this->Attachment->find('first', array(
                    'conditions' => array(
                        'Attachment.id' => $attach[$i]['id'],
                    ),));
                $fname[$i] = $a['Attachment']['alternative'] . '.' . $a['Attachment']['extension'];
                if ($attach[$i]['remove'] == '1') {
                    $fname[$i] = $fname[$i] . '(削除)';
                }
            } 
            if (isset($attach[$i]['file'])) {
                if (empty($fname[$i])){
                    $fname[$i] = $attach[$i]['file']['name'];
                    if (!empty($fname[$i])) {
                        $fname[$i] = $fname[$i] . '(追加)';
                    }
                 } else {
                    $f = $attach[$i]['file']['name'];
                    if (!empty($f)) {
                        $fname[$i] = $f . '(変更)';
                    }
                 }
            }
        }
        return $fname;
    }

    function _download($yyyy)
    {
        $basename  = 'accessLog' . $yyyy . ".csv";
        if (!is_readable(DOWNLOADS . $basename)) {
            return false;
        }
        $alternative = '操作ログ_'. $yyyy;
        $this->viewClass = 'Media';
        $params = array(
            'id'        => $basename,
            'name'      => mbo($alternative),
            'download'  => true,
            'extension' => 'csv',
            'path'      => DOWNLOADS,
        );
        $this->set($params);
        return true;
    }
}
