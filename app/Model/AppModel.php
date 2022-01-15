<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    var $prev_record = null;
    var $set_prev_record = false;
    function beforeSave($options = Array())
    {
        $this->setPrevRecord();
        return parent::beforeSave($options);
    }
    
    function beforeDelete($cascade = true)
    {
        $this->setPrevRecord();
        return parent::beforeDelete($cascade);
    }
    
    // @return void
    function setPrevRecord()
    {
        if (!$this->set_prev_record) return;
        if (!empty($this->id)) {
            $this->prev_record = $this->findById($this->id);
        } else {
            $this->prev_record = null;
        }
    }
}
