<?
class show_demand extends TemplateWidgets {
    function __construct($action_method,$vars=array()) {
        $this->$action_method($vars);
    }

    private function block($vars) {
        $P = inputData::init();

        $P->did = $this->vars['did'] = !empty($vars['did']) ? $vars['did'] : $P->did;
        $P->oper_type = !empty($vars['oper_type']) ? $vars['oper_type'] : $P->oper_type;

        sValidate::isIntWidth($P->did,10);

        if(!sValidate::$code) {
            switch($P->oper_type) {

                case 'exchange':
                    $demand_info = dataBase::DBexchange()->select('demand','*','where did='.$P->did);
                    if (!empty($demand_info)) {
                        //вывод кошелька на кот. будет выполняться перевод
                        $purse = dataBase::DBexchange()->select('balance','purse,desc_val',"where name='".$demand_info[0]['ex_output']."'");
                        $desc_pay = "Direction of the exchange: {$demand_info[0]['ex_output']}->{$demand_info[0]['ex_input']}, ID:{$demand_info[0]['did']}";
                        $cur_in_info = dataBase::DBexchange()->select('balance','desc_val',"where name='".$demand_info[0]["ex_input"]."'");
                        $edit_out_val = round($demand_info[0]['out_val']);
                        $demand_info[0]['add_date'] = date('d.m.Y H:i:s',$demand_info[0]['add_date']);

                        if(!empty($demand_info[0]["coment"]) && $demand_info[0]['status'] != 'y') {
                            $demand_info[0]['comment'] = parent::iterate_tmpl('exchanger',__CLASS__,'comment',array('comment'=>$demand_info[0]['coment']));
                        }

                        $instruction = parent::load_tmpl_block('info.instruction',array(
                            'oper_type'=>$P->oper_type,
                            'status'=>$demand_info[0]["status"],
                            'ex_output'=>$demand_info[0]["ex_output"],
                            'edit_out_val'=>$edit_out_val,
                        ));

                        if ($demand_info[0]["status"] == "n") {
                            $sel_idpay = dataBase::DBadmin()->select('id_payment','id_pay',"where did=".$P->did);
                            if($demand_info[0]['ex_output'] == "EasyPay") {
                                $purse_out = $demand_info[0]["purse_out"];
                            } else {
                                $purse_out = $purse[0]['purse'];
                            }

                            $submit = Vitalis::tmpl('Widgets')->load_tmpl_block('webmoney.paramerty_payment',array(
                                'output'=>$demand_info[0]["ex_output"],
                                'input'=>$demand_info[0]["ex_input"],
                                'in_val'=>$demand_info[0]["in_val"],
                                'purse_out'=>$purse_out,
                                'purse_in'=>$demand_info[0]["purse_in"],
                                'out_val'=>$demand_info[0]["out_val"],
                                'id_pay'=>$sel_idpay[0]["id_pay"],
                                'desc_pay'=>$desc_pay,
                                'did'=>$P->did,
                                'type_action'=>'exchange'
                            ));
                        }

                        $demand_info[0]['status'] = sFormatData::formatStatus($demand_info[0]["status"]);
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'exchange_form',array_merge($demand_info[0],array(
                            'purse'=>$purse[0]['purse'],
                            'purse_desc_val' =>$purse[0]['desc_val'],
                            'cur_in_info' => $cur_in_info[0]['desc_val'],
                            'cur_output' => sConstructor::cur_output($demand_info[0]["ex_output"],$demand_info[0]["purse_out"]),
                            'cur_input' => sConstructor::cur_input($demand_info[0]["ex_input"],$demand_info[0]["purse_in"]),
                            'submit' => $submit,
                            'instruction'=>$instruction
                        )));
                    } else {
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'error',array('error_message'=>$vars['L_demand_error']));
                    }

                    break;
                case 'oplata':
                    $demand_info = dataBase::DBpaydesk()->select('demand_uslugi','*','where did='.$P->did);
                    if (!empty($demand_info)) {
                        $purse = dataBase::DBexchange()->select('balance','purse,desc_val',"where name='".$demand_info[0]['output']."'");
                        $us = dataBase::DBpaydesk()->select('uslugi','name_cat,desc_val','where status=1 and alias_url="'.$demand_info[0]['name_uslugi'].'"');
                        $demand_info[0]['desc_uslugi'] = $us[0]['desc_val'];
                        $desc_pay = "Payment facilities: {$demand_info[0]['name_uslugi']}, ID:{$demand_info[0]['did']}";
                        $cur_in_info = dataBase::DBexchange()->select('balance','desc_val',"where name='".$demand_info[0]["output"]."'");
                        $demand_info[0]['add_date'] = date('d.m.Y H:i:s',$demand_info[0]['add_date']);
                        $demand_info[0]['desc_val'] = $purse[0]['desc_val'];
                        $edit_out_val = round($demand_info[0]['out_val']);

                        if(!empty($demand_info[0]["coment"]) && $demand_info[0]['status'] != 'y') {
                            $demand_info[0]['comment'] = parent::iterate_tmpl('exchanger',__CLASS__,'comment',array('comment'=>$demand_info[0]['coment']));
                        }

                        $instruction = parent::load_tmpl_block('info.instruction',array(
                            'oper_type'=>'oplata',
                            'status'=>$demand_info[0]["status"],
                            'ex_output'=>$demand_info[0]["output"],
                            'edit_out_val'=>$edit_out_val,
                        ));

                        if ($demand_info[0]["status"] == "n") {
                            $sel_idpay = dataBase::DBadmin()->select('id_payment','id_pay',"where did=".$P->did);
                            if($demand_info[0]['output'] == "EasyPay") {
                                $purse_out = $demand_info[0]["purse_out"];
                            } else {
                                $purse_out = $purse[0]['purse'];
                            }

                            $submit = Vitalis::tmpl('Widgets')->load_tmpl_block('webmoney.paramerty_payment',array(
                                'output'=>$demand_info[0]["output"],
                                'input'=>$demand_info[0]["ex_input"],
                                'in_val'=>$demand_info[0]["in_val"],
                                'purse_out'=>$purse_out,
                                'purse_in'=>$demand_info[0]["purse_in"],
                                'out_val'=>$demand_info[0]["out_val"],
                                'id_pay'=>$sel_idpay[0]["id_pay"],
                                'desc_pay'=>$desc_pay,
                                'did'=>$P->did,
                                'type_action'=>'oplata'
                            ));
                        }

                        $demand_info[0]['status'] = sFormatData::formatStatus($demand_info[0]["status"]);
                        $data_model = Model::getStaticData('uslugi','form',$demand_info[0]['name_uslugi']);
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'oplata_form',array_merge($demand_info[0],array(
                            'purse'=>$purse[0]['purse'],
                            'purse_desc_val' =>$purse[0]['desc_val'],
                            'cur_in_info' => $cur_in_info[0]['desc_val'],
                            'name_uslugi' =>$data_model['account_name'],
                            'submit' => $submit,
                            'instruction'=>$instruction
                        )));

                    }
                    else {
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'error',array('error_message'=>$vars['L_demand_error']));
                    }
                    break;
                case 'refill':

                    $demand_info = dataBase::DBpaydesk()->select('demand_cash','*','where did='.$P->did);

                    if (!empty($demand_info)) {
                        //вывод кошелька на кот. будет выполняться перевод
                        $purse = dataBase::DBexchange()->select('balance','purse,desc_val',"where name='".$demand_info[0]['ex_output']."'");
                        $cur_in_info = dataBase::DBexchange()->select('balance','desc_val',"where name='".$demand_info[0]["ex_input"]."'");
                        $edit_out_val = round($demand_info[0]['out_val']);
                        $demand_info[0]['add_date'] = date('d.m.Y H:i:s',$demand_info[0]['add_date']);

                        if(!empty($demand_info[0]["coment"]) && $demand_info[0]['status'] != 'y') {
                            $demand_info[0]['comment'] = parent::iterate_tmpl('exchanger',__CLASS__,'comment',array('comment'=>$demand_info[0]['coment']));
                        }

                        $instruction = parent::load_tmpl_block('info.instruction',array(
                            'oper_type'=>$P->oper_type,
                            'status'=>$demand_info[0]["status"],
                            'edit_out_val'=>$edit_out_val,
                        ));

                        if ($demand_info[0]["status"] == "n") {
                            $submit = parent::iterate_tmpl('exchanger',__CLASS__,'confirm_submit',array('did'=>$P->did,'oper_type'=>'refill'));
                        }

                        $demand_info[0]['status'] = sFormatData::formatStatus($demand_info[0]["status"]);
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'refill_form',array_merge($demand_info[0],array(
                            'purse'=>$purse[0]['purse'],
                            'purse_desc_val' =>$purse[0]['desc_val'],
                            'cur_in_info' => $cur_in_info[0]['desc_val'],
                            'cur_output' => sConstructor::cur_output($demand_info[0]["ex_output"],$demand_info[0]["purse_out"]),
                            'cur_input' => sConstructor::cur_input($demand_info[0]["ex_input"],$demand_info[0]["purse_in"]),
                            'submit' => $submit,
                            'instruction'=>$instruction
                        )));
                    } else {
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'error',array('error_message'=>$vars['L_demand_error']));
                    }

                    break;
                case 'shop':

                    break;
            }
        } else {
            $this->vars['html'] = $vars['L_demand_error'];
        }



        return $this->vars;
    }

    private function process($vars) {
        $P = inputData::init();
        $status = 0;

        sValidate::isIntWidth($P->did,10,'L_bad_did');

        switch($P->oper_type) {

            case 'refill':

                if(!sValidate::$code) {
                    $info = dataBase::DBpaydesk()->select('demand_cash','status','where did='.$P->did);
                    if($info[0]['status'] == 'n') {
                        dataBase::DBpaydesk()->update('demand_cash',array('status'=>'yn'),'where did='.$P->did);
                        $message = $vars['L_confirm_ok'];
                    } elseif($info[0]['status'] == 'yn') {
                        $status = 1;
                        $message = $vars['L_confirm_process'];
                    } elseif($info[0]['status'] == 'y') {
                        $message = $vars['L_confirm_payed'];
                    } else {
                        $status = 1;
                        $message = $vars['L_confirm_error'];
                    }

                } else {
                    $status = 1; $message = sValidate::$message;
                }

                break;

        }

        echo json_encode(array('status'=>$status,'message'=>$message));
    }
}

?>