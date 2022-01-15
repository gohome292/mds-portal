<?php
class MdsOptionsManagerComponent extends Component
{
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @return void
    function LevelIndex()
    {
        $_this = $this->controller;
        
        $levels = array(
            '5' => '全ユーザに公開',
            '4' => '第4階層以上',
            '3' => '第3階層以上',
            '2' => '第2階層以上',
            '1' => '第1階層以上',
        );
        
        $_this->set(compact('levels'));
    }
    
    // @return void
    function RegularIndex()
    {
        $_this =& $this->controller;
        
        $regulars = array(
            '' => '全て表示',
            '1' => '常時表示',
            '0' => '常時表示以外',
        );
        
        $_this->set(compact('regulars'));
    }
}
