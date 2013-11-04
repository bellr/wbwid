<?
class paramerty_payment extends TemplateWidgets {

    function __construct($action_method,$vars) {
        $this->$action_method($vars);
    }

    private function block($vars) {

        $vars['hesh_easypay'] = md5($vars['did'].$vars['purse_out'].$vars['out_val'].Config::$wmBase['s_k']);
		$output = $vars['output'];
        if ($output == "WMZ" || $output == "WMR" || $output == "WME" || $output == "WMG" || $output == "WMU" || $output == "WMY" || $output == "WMB") {$output = "WMT";}
        $vars['wmid'] = Cookies::get('wmid');

		$this->vars['form'] = parent::iterate_tmpl('webmoney',__CLASS__,strtolower($output),$vars);

        return $this->vars;
    }
}


?>