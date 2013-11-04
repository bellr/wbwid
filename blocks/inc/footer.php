<?
class footer extends TemplateWidgets {

    function __construct($action_method,$vars) {
        $this->$action_method();
    }

    private function block() {
        $default_wmid = Config::$wmBase['default_wmid'];
		$this->vars['date'] = date('Y');
        $this->vars['wmid'] = Config::$wmBase[$default_wmid];

        return $this->vars;
    }
}
