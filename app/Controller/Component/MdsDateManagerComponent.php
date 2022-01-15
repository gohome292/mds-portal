<?php
class MdsDateManagerComponent extends Component
{
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @param integer $top_customer_organization_id
    // @return array
    function getOptions_YearMonth($top_customer_organization_id = null)
    {
        $_this = $this->controller;
        if (empty($top_customer_organization_id)) {
            $starttime = Configure::read('Mds.startYearMonthTime');
        } else {
            $start_year_month = $_this->Session->read('Auth.User.nav.start_year_month');
            $start_year_month =
                $start_year_month . '/01 00:00:00';
            $starttime = strtotime($start_year_month);
        }
        $endtime = $this->getEndtime();
        $year_months = array();
        while($starttime <= $endtime) {
            $year_months[date('Ym', $endtime)] = date('Y年n月', $endtime);
            $endtime = strtotime('-1 month', $endtime);
        }
        return $year_months;
    }
    
    // @return integer
    function getEndtime()
    {
        return strtotime(date('Y/m/01 00:00:00'));
    }
}
