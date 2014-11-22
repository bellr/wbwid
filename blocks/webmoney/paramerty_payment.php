<?
class paramerty_payment extends TemplateWidgets {

    public function block($P) {

        $P = $P->vars;
		$output = $P['output'];

        if ($output == "WMZ" || $output == "WMR" || $output == "WME" || $output == "WMG" || $output == "WMU" || $output == "WMY" || $output == "WMB") {

            $PP = Extension::Payments()->getParam('payments','webmoney');
            $vars['demo_purse'] = $PP->WMID['184190489368'][$output];

            $output = "WMT";
        }

        if($output == 'WMB') $tmpl_direct = 'direct';
        else                 $tmpl_direct = 'bill';

        $vars['wmid'] = Cookies::get('wmid');
        $vars['type_action'] = $P['type_action'];
        $vars['output_system'] = $output;
        $vars['signature'] = Model::Demand()->createSignature($P);
        $vars['content_payment_type'] = $this->iterate_tmpl('webmoney',__CLASS__,$tmpl_direct.'_payment',array_merge($vars,$P));

		$this->vars['form'] = $this->iterate_tmpl('webmoney',__CLASS__,strtolower($output),array_merge($vars,$P));

        return $this;
    }
}


?>