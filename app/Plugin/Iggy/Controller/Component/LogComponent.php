<?php
class LogComponent extends Component
{
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @param mixed $var
    // @return void
    function run($var = '')
    {
        $_this = $this->controller;
        if (!is_string($var)) $var = var_export($var, true);
        $debug = array_shift(debug_backtrace());
        $text = sprintf(
            '[%s](%s)[%s]%s',
            basename($debug['file']),
            $debug['line'],
            round(memory_get_usage() / 1024 / 1024, 2) . 'MB',
            "\n{$var}"
        );
        $_this->log($text);
    }
}
