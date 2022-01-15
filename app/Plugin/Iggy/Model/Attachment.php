<?php
class Attachment extends AppModel
{
    var $actsAs = array(
        'Iggy.Attachment',
        'Iggy.Log',
    );
    var $valids;
    var $set_prev_record = true;
    
    function loadValidate()
    {
        if (empty($this->valids)) {
            $this->valids = fgetyml('attachments_valids');
        }
        $model      = $this->request->data['Attachment']['model'];
        $identifier = $this->request->data['Attachment']['identifier'];
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
        
        if (!empty($this->request->data['Attachment']['model']['attach'])) {
            unset($valid['size']);
        }
        
        $this->setValidate($valid);
    }
}
