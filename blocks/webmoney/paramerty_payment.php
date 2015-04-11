<?
class paramerty_payment extends TemplateWidgets {

    public function block($P) {

        $P = $P->vars;
		$output = $tmpl_direct = $P['output'];
        $tmpl_form = strtolower($P['output']);

        if ($output == "WMZ" || $output == "WMR" || $output == "WME" || $output == "WMG" || $output == "WMU" || $output == "WMY" || $output == "WMB") {

            $PP = Extension::Payments()->getParam('payments');
            $vars['url_wm_merchant'] = $PP->url_wm_merchant;

            $output = "WMT";

            if($output == 'WMB') $tmpl_form = 'bill_payment';
            else                 $tmpl_form = 'direct_payment';
        }

        $vars['did_hash'] = Model::Demand()->didHesh($P['did']);
        $vars['wmid'] = Cookies::get('wmid');
        $vars['type_action'] = $P['type_action'];
        $vars['output_system'] = $output;
        $vars['signature'] = Model::Demand()->createSignature($P,'sha256');

		$this->vars['form'] = $this->iterate_tmpl('webmoney',__CLASS__,$tmpl_form,array_merge($vars,$P));

        return $this;
    }
}


?>