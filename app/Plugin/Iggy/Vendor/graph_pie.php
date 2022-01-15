<?php
// @param array $records ex) array('1月' => 10, '2月' => 3, '3月' => 5)
// @return void
function graph_pie($records)
{
    define('CHART_WIDTH', 400);
    define('CHART_HEIGHT', 200);
    define('CHART_MARGIN', 5);
    
    $DataSet = new pData;
    $DataSet->AddPoint(array_values($records),'Serie1');
    $DataSet->AddPoint(array_keys($records),'Serie2');
    $DataSet->AddAllSeries();
    $DataSet->SetAbsciseLabelSerie('Serie2');
    
    $Chart = new pChart(CHART_WIDTH,CHART_HEIGHT);
    /*$Chart->setFontProperties(FONTS . 'mona.ttf', 11);
    $Chart->drawFilledRoundedRectangle(CHART_MARGIN+2,CHART_MARGIN+2,CHART_WIDTH-CHART_MARGIN-2,CHART_HEIGHT-CHART_MARGIN-2,CHART_MARGIN,240,240,240);
    $Chart->drawRoundedRectangle(CHART_MARGIN,CHART_MARGIN,CHART_WIDTH-CHART_MARGIN,CHART_HEIGHT-CHART_MARGIN,CHART_MARGIN,230,230,230);*/
    $Chart->setFontProperties(FONTS . 'mona.ttf', 11);
    $Chart->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),CHART_MARGIN*10+(CHART_WIDTH-CHART_MARGIN*10)/3,CHART_HEIGHT/2-CHART_MARGIN*2,(CHART_WIDTH-CHART_MARGIN*10)/3,PIE_PERCENTAGE,TRUE,50,20,5);
    $Chart->drawPieLegend(CHART_WIDTH-CHART_MARGIN*14,CHART_MARGIN*3,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
    
    //$Chart->Render('pchart.png');
    $Chart->Stroke();
}
