<?php
class ReportAssistComponent extends Component
{
    var $protect;
    
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @param string $identifier
    // @param object $model
    // @param array $options
    // @return void
    function output($identifier, $model, $options = array())
    {
        $_this = $this->controller;
        
        if (!isset($_this->Report)) $_this->loadModel('Report');
        $_this->Report->recursive = 0;
        $params = array(
            'fields' => array(
                'Report.id',
                'Report.name',
                'PrintForm.identifier',
            ),
            'conditions' => array(
                'Report.identifier' => $identifier,
            ),
        );
        $report = $_this->Report->find('first', $params);
        
        if (!isset($_this->ReportDetail)) $_this->loadModel('ReportDetail');
        $_this->ReportDetail->recursive = 0;
        $params = array(
            'fields' => array(
                'ReportDetail.id',
                'ReportDetail.name',
                'ReportDetail.field',
                'ReportDetail.size',
                'Align.identifier',
            ),
            'conditions' => array(
                'ReportDetail.report_id' => $report['Report']['id'],
            ),
            'order' => array(
                'ReportDetail.sort' => 'ASC',
            ),
        );
        $report_details = $_this->ReportDetail->find('all', $params);
        
        if (!isset($_this->Attachment)) $_this->loadModel('Attachment');
        $_this->Attachment->recursive = -1;
        $params = array(
            'fields' => array(
                'Attachment.basename',
            ),
            'conditions' => array(
                'Attachment.model'       => 'Report',
                'Attachment.foreign_key' => $report['Report']['id'],
            ),
        );
        $template = '_';
        if ($attachment = $_this->Attachment->find('first', $params)) {
            $template = UPLOADS . $attachment['Attachment']['basename'];
        }
        
        $records = $model->find('all', $options);
        
        App::import('Vendor', 'pdf/' . strtolower($identifier));
        $classname = 'PDF_' . $identifier;
        $pdf = new $classname;
        if (!empty($this->protect)) {
            $pdf->SetProtection(
                $this->protect[0],
                $this->protect[1],
                $this->protect[2]
            );
        }
        $pdf->prepare($report, $report_details);
        if (is_readable($template)) {
            $pdf->setSourceFile($template);
            $pdf->useTemplate($pdf->ImportPage(1));
        }
        $pdf->make($records);
        $filename = "{$_this->Auth->user('id')}.tmp";
        $pdf->Output(DOWNLOADS . $filename);
        
        $_this->view = 'Media';
        $params = array(
            'id'        => $filename,
            'name'      => $report['Report']['name'],
            'download'  => true,
            'extension' => 'pdf',
            'path'      => DOWNLOADS,
        );
        $_this->set($params);
    }
    
    // @param array $permissions
    // @param string $user_pass
    // @param string $owner_pass
    // @return void
    function protect(
        $permissions = array(),
        $user_pass = null,
        $owner_pass = null
    )
    {
        $this->protect = array(
            $permissions,
            $user_pass,
            $owner_pass,
        );
    }
}
