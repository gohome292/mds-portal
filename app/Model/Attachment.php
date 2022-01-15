<?php
class Attachment extends AppModel
{
    var $actsAs = array(
        'Iggy.Attachment',
        //'Iggy.Log',
    );
    var $valids;
    var $set_prev_record = true;
    
    function loadValidate()
    {
        if (empty($this->valids)) {
            $this->valids = fgetyml('Attachments_valids');
        }
        $model      = $this->data['Attachment']['model'];
        $identifier = $this->data['Attachment']['identifier'];
        $valid = array(
            'extension' => sprintf(
                'required | single | maxLen[5] | inList[%s]',
                implode(',', $this->valids[$model][$identifier]['extensions'])
            ),
            'size' => sprintf(
                'required | numeric | range[0,%s]',
                intval($this->valids[$model][$identifier]['size']) * 1024
            ),
        );
        
        if (!empty($this->data['Attachment']['model']['attach'])) {
            unset($valid['size']);
        }
        
        $this->validates($valid);
    }
}
