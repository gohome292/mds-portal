<?php
class DriverManualTypesController extends AppController
{
    var $uses = array(
        'DriverManualType',
        'CustomerOrganization',
        'User',
    );
    var $title = '分類情報編集';
    var $components = array(
        'ActionCommon',
        'ActionAdd',
        'ActionEdit',
        'ActionSave',
        'ActionRemove',
    );
    
    // @param integer $id
    function edit($id = null)
    {
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Menu';
        $this->ActionCommon->setMenu();
        $this->ActionCommon->setAcl(array('edit', 'remove'));
        
        //$this->request->data['DriverManualType']['id'] = $id;
        if(empty($id)){
            $record = array();
        } else {
            $record = $this->DriverManualType->findById($id);
        }

        if(empty($this->request->named['customer_organization_id'])){
            if(empty($record)){
                $customer_organization_id = $this->request->data['DriverManualType']['customer_organization_id'];
            } else {
                $customer_organization_id = $record['DriverManualType']['customer_organization_id'];
            }
        } else {
            $customer_organization_id = $this->request->named['customer_organization_id'];
        }
        
        if(empty($this->request->named['driver_manual_id'])){
            if(empty($record)){
                $driver_manual_id = $this->request->data['DriverManualType']['driver_manual_id'];
            } else {
                $driver_manual_id = $record['DriverManualType']['driver_manual_id'];
            }
        } else {
            $driver_manual_id = $this->request->named['driver_manual_id'];
        }
        

        if($driver_manual_id == '1'){
            $back_url = '/adm_drivers/index/' . $customer_organization_id . '/driver_manual_type_id:';
            $breadname = 'プリンタドライバ > 一覧 > 分類情報編集 ';
        } else if ($driver_manual_id == '2'){
            $back_url = '/adm_manuals/index/' . $customer_organization_id . '/driver_manual_type_id:';
            $breadname = 'マニュアル > 一覧 > 分類情報編集 ';
        }
        if(empty($id)){
            $breadname .= '> 登録';
        } else {
            $breadname .= '> 編集';
        }

        $this->auto_breadcrumbs = false;
        $this->breadcrumbs = $breadname;
        $this->set('breadcrumbs', $this->breadcrumbs);
        
        $this->set('backurl',$back_url . $id . '/');
        // 追加
        if(empty($id)){
            $this->request->data['DriverManualType']['driver_manual_id'] = $driver_manual_id;
            $this->request->data['DriverManualType']['customer_organization_id'] = $customer_organization_id;
            if($this->ActionAdd->run(array('flash' => false, 'no_render' => true))){
                $message = '保存しました。';
                $this->flash(
                    $message,
                    "{$back_url}{$this->model->id}/",
                    Configure::read('App.pause'));
            } else {
                $this->set('backurl', $back_url . '0/');
                $this->set(compact('customer_organization_id', 'driver_manual_id'));
            }

        // 編集
        } else {
            if ($this->ActionEdit->run($id, array('flash' => false))){
                $message = '保存しました。';
                $this->flash(
                    $message,
                    "{$back_url}{$id}/",
                    Configure::read('App.pause'));
            } else {
                $this->set('backurl',$back_url . '0/');
                $this->set(compact('customer_organization_id', 'driver_manual_id'));
            }
        }
    }
    
    function remove($id){
    //$this->ActionRemove->run($id);

        $remove_rst = $this->DriverManualType->findById($id);
        
        $driver_manual_id = $remove_rst['DriverManualType']['driver_manual_id'];
        
        $return_url = null;
        
        if($driver_manual_id == 1){
            $return_url = '/adm_drivers/index/';
        } elseif ($driver_manual_id == 2){
            $return_url = '/adm_manuals/index/';
        }
        
        $result = $this->DriverManualType->delete($id, true);
        
        if (Configure::read('App.simple_message')) {
            $message = '削除しました。';
        } else {
            $message = "ID:{$id}を削除しました。";
        }

        $this->flash(
            $message,
            "{$return_url}",
            Configure::read('App.pause')
        );
        return true;
    
    }
    
}
