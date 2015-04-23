<?
class creat_demand extends TemplateWidgets {

    public function block($P) {

        $PP = (array)Extension::Payments()->getParam('payments');

		if(empty($P->object)) {Browser::go404();}
            switch($P->controller) {

                case 'exchange':
                    $p = explode('_',$P->object);
                    $output = $this->vars['output'] = $p[0];
                    $input = $this->vars['input'] = $p[1];
                    $direction = $P->object;
                    $info_output = dataBase::DBexchange()->select('balance','desc_val,com_seti',"where name='{$output}'");
                    $info_input = dataBase::DBexchange()->select('balance','balance,desc_val,com_seti',"where name='{$input}'");
                    $u = dataBase::DBexchange()->select('kurs','konvers,direct',"where direction='{$direction}'");

                    $balance = $info_input[0]["balance"] - $info_input[0]["balance"] * $info_input[0]["com_seti"];
                    $sel_d = dataBase::DBmain()->select('discount','amount,size_d',"where indef='{$input}'");
                    $com_seti = $info_input[0]["com_seti"];
                    $this->vars['desc_val_in'] = $info_input[0]['desc_val'];
                    break;

                case 'oplata':

                    $uslugi = Model::Uslugi()->get(array(
                        'alias_url' => $P->object
                    ));

                    $path_kurs = isset($uslugi['alias_kurs']) ? $uslugi['alias_kurs'] : 'usluga';
                    $direction = $P->oplata.'_'.$path_kurs;

                    $u = dataBase::DBexchange()->select('kurs','konvers,direct',"where direction='{$direction}'");
                    $info_input = dataBase::DBexchange()->select('balance','balance,desc_val,com_seti',"where name='{$P->oplata}'");
                    $com_seti = $info_input[0]["com_seti"];
                    $this->vars['type_cur'] = $P->oplata;
                    $this->vars['ex_in'] = 'usluga';
                    $this->vars['desc_val_in'] = $PP['by_nal'];
                    $balance = Model::Balance('HOME')->getPurseService();
                    $this->vars['name_uslugi'] = $tmpl = $P->object;


                    if ($P->object == 'ByFly_Minsk' || $P->object == 'ByFly_Minsk_region' || $P->object == 'ByFly_Gomel' || $P->object == 'ByFly_Mogilev' || $P->object == 'ByFly_Vitebsk' || $P->object == 'ByFly_Brest' || $P->object == 'ByFly_Grodno') {$tmpl = 'ByFly';}
                    //elseif ($P->object == 'Beltelecom_Minsk' || $P->object == 'Beltelecom_Brest' || $P->object == 'Beltelecom_Minsk_region' || $P->object == 'Beltelecom_Grodno' || $P->object == 'Beltelecom_Vitebsk' || $P->object == 'Beltelecom_Mogilev' || $P->object == 'Beltelecom_Mogilev_region'  || $P->object == 'Beltelecom_Gomel') {$P->object = 'Beltelecom';}

                    $data_model = Model::getStaticData('uslugi','form',$P->object);

                    $this->vars['inside_input'] = parent::iterate_tmpl('exchanger',__CLASS__,'form/'.$tmpl,array(
                        $P->object.'_1' => Cookies::get($P->object.'_1'),
                        $P->object.'_2' => Cookies::get($P->object.'_2'),
                        'wmid' => Cookies::get('wmid'),
                        'account_name' => $data_model['account_name'],
                        'maxlength' => $data_model['maxlength'],
                        'size' => $data_model['maxlength'] + 1,
                    ));

                    break;

                case 'refill':
                    $direction = 'NAL_'.$P->object;

                    $input = $this->vars['input'] = $P->object;
                    $this->vars['tile_form'] = $P->vars['L_title_refill'];
                    $this->vars['type_cur'] = $PP['by_nal'];
                    $info_input = dataBase::DBexchange()->select('balance','balance,desc_val,com_seti',"where name='{$input}'");
                    $this->vars['desc_val_in'] = $info_input[0]['desc_val'];
                    $u = dataBase::DBexchange()->select('kurs','konvers,direct',"where direction='{$direction}'");

                    $balance = $info_input[0]["balance"] - $info_input[0]["balance"] * $info_input[0]["com_seti"];
                    $sel_d = dataBase::DBmain()->select('discount','amount,size_d',"where indef='{$input}'");
                    $com_seti = 0;

                    break;

                case 'deposit_forex':

                    $d = explode('_',$P->object);
                    $direction = 'NAL_forex'.$d[1];

                    $balance_name = Model::Balance()->getTransform($d[0],$d[1]);

                    $this->vars['input'] = $input = $d[0];
                    $this->vars['currency'] = $d[1];
                    $this->vars['tile_form'] = $P->vars['L_title_refill'];
                    $this->vars['type_cur'] = $PP['by_nal'];

                    $info_input = dataBase::DBpaydesk()->select('uslugi','desc_val',"where name='{$d[0]}'");
                    $this->vars['desc_uslugi'] = $info_input[0]['desc_val'];
                    $info_input[0]['desc_val'] = strtoupper($d[1]);
                    $this->vars['desc_val_in'] = $info_input[0]['desc_val'];

                    $u = dataBase::DBexchange()->select('kurs','konvers,direct',"where direction='{$direction}'");

                    $balance = Model::Balance()->getBalanceForPay($balance_name);
                    //$sel_d = dataBase::DBmain()->select('discount','amount,size_d',"where indef='{$input}'");
                    $com_seti = 0;

                    break;
            }

            $this->vars['desc_val_out'] = $info_output[0]['desc_val'];
            $this->vars['format_balance'] = number_format($balance, 2, '.', ' ');
            $this->vars['email'] = Cookies::get('email');
            $this->vars['Constructor'] = swConstructor::check($output,$input);
            $this->vars['direct'] = $u[0]["direct"];
            $this->vars['konvers'] = $u[0]["konvers"];
            $this->vars['comission'] = $com_seti;
            $this->vars['discount'] = json_encode($sel_d);
            $this->vars['controller'] = $P->controller;

            $this->tmplName = $this->vars['type_operation'] = $P->controller;

        return $this;
    }

    public function process($P) {

        sValidate::Limit('max','inval',$P->ex_out,$P->in_val,'L_max_inval');
        sValidate::Limit('min','inval',$P->ex_out,$P->in_val,'L_min_inval');
        sValidate::Limit('max','outval',$P->ex_in,$P->out_val,'L_max_outval');
        sValidate::Limit('min','outval',$P->ex_in,$P->out_val,'L_min_outval');

        sValidate::isNumeric($P->out_val);
        sValidate::isNumeric($P->in_val);
        if(isset($P->wmid)) {sValidate::isIntWidth($P->wmid,12,'L_bad_wmid');}
        if(isset($P->p_output)) {sValidate::Purse($P->p_output,$P->ex_out);}

        if(isset($P->p_input)) {

        	sValidate::Purse($P->p_input,$P->ex_in);

        }

        sValidate::Email($P->email);
        swValidate::Balance($P->ex_in,$P->out_val,$P->currency);
        sValidate::checkBox($P->rules,'L_error_checkbox');

        if(!sValidate::$code) {
            $direction = $P->ex_out.'_'.$P->ex_in;

            $in_val = Model::Kurs('HOME')->checkKurs($direction,$P->out_val,$P->in_val,$P->ex_in);
            if(isset($in_val)) {$P->out_val = $in_val;}

            switch($P->type_operation) {

                case 'exchange':

                    $data['did'] = $did = swDemand::wm_ReqID();
                    $data['ex_output'] = $P->ex_out;
                    $data['ex_input'] = $P->ex_in;
                    $data['out_val'] = $P->in_val;
                    $data['in_val'] = $P->out_val;
                    $data['purse_out'] = $P->p_output;
                    $data['purse_in'] = $P->p_input;
                    $data['email'] = $P->email;
                    $data['add_date'] = time();
                    $data['partner_id'] = Cookies::get('partner_id');

                    Model::Demand()->insert($did,'demand',$data);

                    Cookies::set('p_out_'.$P->ex_out,$P->p_output);
                    Cookies::set('p_in_'.$P->ex_in,$P->p_input);
                    Cookies::set('wmid',$P->wmid);
                    Cookies::set('email',$P->email);

                    break;

                case 'oplata':

                    $data['did'] = $did = swDemand::wm_ReqID();
                    $data['output'] = $P->ex_out;
                    $data['name_uslugi'] = $P->name_uslugi;
                    $data['purse_out'] = $P->p_output;
                    $data['out_val'] = $P->in_val;
                    $data['in_val'] = round($P->out_val / 10) * 10;
                    //$data['in_val'] = $P->out_val;
                    $data['pole1'] = $P->pole1;
                    $data['pole2'] = $P->pole2;
                    $data['email'] = $P->email;
                    $data['add_date'] = time();
                    $data['partner_id'] = Cookies::get('partner_id');

                    Model::Demand('HOME')->insert($did,'demand_uslugi',$data);
                    Cookies::set('wmid',$P->wmid);
                    Cookies::set($P->name_uslugi.'_1',$P->pole1);
                    Cookies::set($P->name_uslugi.'_2',$P->pole2);
                    Cookies::set('email',$P->email);

                    break;
                case 'refill':

                    $data['did'] = $did = swDemand::wm_ReqID();
                    $data['output'] = 'pochta';
                    $data['input'] = $P->ex_in;
                    $data['purse_in'] = $P->p_input;
                    $data['out_val'] = round($P->in_val * 0.01)*100;
                    $data['in_val'] = $P->out_val;
                    $data['email'] = $P->email;
                    $data['add_date'] = time();
                    $data['partner_id'] = Cookies::get('partner_id');

                    Model::Demand()->insert($did,'demand_cash',$data);

                    Cookies::set('p_in_'.$P->ex_in,$P->p_input);
                    Cookies::set('wmid',$P->wmid);
                    Cookies::set('email',$P->email);

                    $type = '_refill';
                    break;
                case 'deposit_forex':

                    $data['did'] = $did = swDemand::wm_ReqID();
                    $data['output'] = 'pochta';
                    $data['input'] = $P->ex_in.'_'.$P->currency;
                    $data['purse_in'] = $P->p_input;
                    $data['out_val'] = round($P->in_val * 0.01)*100;
                    $data['in_val'] = $P->out_val;
                    $data['email'] = $P->email;
                    $data['add_date'] = time();
                    $data['partner_id'] = Cookies::get('partner_id');

                    Model::Demand()->insert($did,'demand_cash',$data);

                    Cookies::set('p_in_'.$data['input'],$P->p_input);
                    Cookies::set('email',$P->email);
                    $type = '_refill';
					$P->type_operation = 'refill';

                    break;
            }

            $mail = mailSender::init();
            $mail->to = $P->email;
            $mail->subject = '[WM-RB.net] '.$P->vars['L_subject_mail'];
            $mail->message = $this->iterate_tmpl('emails',Config::getLang(),__CLASS__.$type,array(
                'bottom_support' => parent::iterate_tmpl('emails',Config::getLang(),'bottom_support'),
                'did' => $did,
                'did_href' => Config::$base[PROJECT.'_URL'].'/demand/'.$P->type_operation.'/'.Model::Demand()->didHesh($did).'/'
            ));
            $mail->smtpmail();

            $html = Vitalis::tmpl('Widgets')->load_tmpl_block('exchanger.show_demand',array('did'=>$did,'oper_type'=>$P->type_operation));
        } else {
            sValidate::$code = 1; $message = sValidate::$message;
        }

        return json_encode(array('status'=>sValidate::$code,'message'=>$message,'html'=>$html));
    }
}

?>
