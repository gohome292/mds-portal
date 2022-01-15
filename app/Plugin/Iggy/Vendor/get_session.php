<?php
function &get_session($start = true , $base = null){
    $session = null;
    if(ClassRegistry::isKeySet('CakeSession')){
        $session = ClassRegistry::getObject('CakeSession');
    }else{
        App::import('Core','CakeSession');
        $session = new CakeSession($base,$start);
        
        ClassRegistry::addObject('CakeSession',$session);
    }
    
    if($session === null){
        return $session;
    }
    
    if($start && !$session->started()){
        $session->start();
    }
    
    return $session;
}
