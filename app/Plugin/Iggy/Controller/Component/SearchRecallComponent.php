<?php
class SearchRecallComponent extends Component
{
    var $components = array('Session');
    
    function Initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @return void
    function run()
    {
        $_this = $this->controller;
        if (isset($_this->request->data[$_this->modelClass])) {
            $this->Session->write(
                "Search.{$_this->modelClass}",
                $_this->request->data[$_this->modelClass]
            );
        } else {
            if ($this->Session->check("Search.{$_this->modelClass}")) {
                $_this->request->data[$_this->modelClass] = $this->Session->read(
                    "Search.{$_this->modelClass}"
                );
            }
        }
    }
    
    // @return array
    function getData()
    {
        $_this = $this->controller;
        if ($this->Session->check("Search.{$_this->modelClass}")) {
            return $this->Session->read("Search.{$_this->modelClass}");
        }
        return null;
    }
    
    // @param string $modelClass
    // @return void
    function delete($modelClass = null)
    {
        $_this = $this->controller;
        _default($modelClass, $_this->modelClass);
        $this->Session->delete("Search.{$modelClass}");
    }
}
