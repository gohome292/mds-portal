<?php
App::uses('AuthComponent', 'Controller/Component');
class User extends AppModel
{
    var $actsAs = array(
        'Iggy.BasicValidation',
        'Iggy.AppValidation',
        'Containable',
        'Acl' => array('requester'),
    );
    var $belongsTo = array(
        'Group',
        'CustomerOrganization',
        'TopCustomerOrganization' => array(
            'className'  => 'CustomerOrganization',
            'foreignKey' => 'top_customer_organization_id',
        ),
    );
    var $belongsToTreeModel = array(
        'CustomerOrganization',
    );
    var $action = 'setup';
    var $password;
    
    function loadValidate()
    {
        switch ($this->action) {
        case 'add':
            $valid = array(
                'username'  => 'required | single | between[3,80] | unique',
                'name'      => 'required | maxLen[20]',
                'group_id'  => 'required | inListDB',
                'customer_organization_id' => 'inListDB',
                'email'     => 'required | maxLen[80]',
                'company_name_for_mail' => 'maxLen[100]',
                'person_name_for_mail'  => 'maxLen[100]',
                'contact_address' => 'maxLen[200]',
                'password1' => 'required | single | minLen[8]',
                'password2' => 'required | confirm[password1]',
            );
            // 権限がお客様
            if (!empty($this->data['User']['group_id'])) {
                if ( $this->data['User']['group_id'] == 3) {
                    $valid['customer_organization_id'] = 'required | inListDB';
                    $valid['company_name_for_mail'] = 'required | maxLen[100]';
                    $valid['person_name_for_mail'] = 'required | maxLen[100]';
                } elseif ( $this->data['User']['group_id'] > 3) {
                    $valid['mps_customer_id'] = 'required | maxLen[1024]';
                    if ( $this->data['User']['group_id'] == 5) {
                        $valid['company_name_for_mail'] = 'required | maxLen[100]';
                    }
                }
            }
            break;
        case 'edit':
            $valid = array(
                'username'  => 'required | single | between[3,80] | unique',
                'name'      => 'required | maxLen[20]',
                'group_id'  => 'required | inListDB',
                'customer_organization_id' => 'inListDB',
                'email'     => 'required | maxLen[80]',
                'company_name_for_mail' => 'maxLen[100]',
                'person_name_for_mail'  => 'maxLen[100]',
                'contact_address' => 'maxLen[200]',
                'password1' => 'single | minLen[8]',
                'password2' => 'confirm[password1]',
            );
            if (!empty($this->data['User']['password1'])) {
                $valid['password2'] = 'required | confirm[password1]';
            }
            // 権限がお客様
            if (!empty($this->data['User']['group_id'])) {
                if ( $this->data['User']['group_id'] == 3) {
                    $valid['customer_organization_id'] = 'required | inListDB';
                    $valid['company_name_for_mail'] = 'required | maxLen[100]';
                    $valid['person_name_for_mail'] = 'required | maxLen[100]';
                } elseif ( $this->data['User']['group_id'] > 3) {
                    $valid['mps_customer_id'] = 'required | maxLen[1024]';
                    if ( $this->data['User']['group_id'] == 5) {
                        $valid['company_name_for_mail'] = 'required | maxLen[100]';
                    }
                }
            }
            break;
        case 'self_edit':
            $valid = array(
                'username'  => 'required | single | between[3,80] | unique',
                'name'      => 'required | maxLen[20]',
                'password1' => 'required | single | minLen[8]',
                'password2' => 'required | confirm[password1]',
            );
            break;
        case 'setup':
            $valid = array(
                'username' => 'required | single | between[3,80] | unique',
                'name'     => 'required | maxLen[20]',
                'group_id' => 'required | inListDB',
                'customer_organization_id' => 'inListDB',
                'email'    => 'required | maxLen[80]',
                'company_name_for_mail' => 'maxLen[100]',
                'person_name_for_mail'  => 'maxLen[100]',
                'contact_address' => 'maxLen[200]',
                'password' => 'required | single | minLen[8]',
            );
            break;
        case 'mail_comment':
            $valid = array(
                'freeword1' => 'maxLen[100]',
                'freeword2' => 'maxLen[100]',
            );
            break;
        }
        $this->setValidate($valid);
    }
    
    function beforeSave($options = Array())
    {
        if (!empty($this->password)) {
            $this->data['User']['password'] = $this->password;
        }
        unset(
            $this->data['User']['password1'],
            $this->data['User']['password2']
        );
        return parent::beforeSave();
    }
    
    function save($data = null, $validate = true, $fieldList = array())
    {
        if (parent::save($data, $validate, $fieldList)) {
            $conditions = array(
                'model'       => $this->name,
                'foreign_key' => $this->id,
            );
            App::import('Component', 'Acl');
            $Aro = new Aro;
            $Aro->id = $Aro->field('id', $conditions);
            if (!empty($this->data[$this->name]['group_id'])) {
                $Aro->saveField(
                    'parent_id',
                    $this->data[$this->name]['group_id']
                );
            }
            $Aro->saveField('alias', "{$this->name}::{$this->id}");
            //$Aro->saveField('alias', $this->data['User']['username']);
            return true;
        }
        return false;
    }
    
    function parentNode()
    {
        if (empty($this->id)) return null;
        $params = array(
            'fields' => array(
                'Group.id',
            ),
            'conditions' => array(
                'User.id' => $this->id,
            ),
            'recursive' => 1,
        );
        return $this->find('first', $params);
    }
}
