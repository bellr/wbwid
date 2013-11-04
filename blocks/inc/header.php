<?
class header extends Template {
    function __construct($action_method,$vars='') {

        $this->$action_method();
    }

    private function block() {

        return $this->vars = $vars;
    }
}

?>