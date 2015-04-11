<?
class show_demand extends TemplateWidgets {

    public function title_form($P) {

        $this->block($P);

        return $this;

    }

    public function show($P) {

        $P->did = isset($P->did_hash) ? $P->did_hash : Model::Demand()->didFromHesh($P->object_id);
        $P->oper_type = $P->object;

        $this->tmplName = 'show_demand';

        $this->block($P);

        return $this;
    }

    public function block($P) {

        $P->did = $this->vars['did'] = !empty($P->vars['did']) ? $P->vars['did'] : $P->did;
        $P->oper_type = !empty($P->vars['oper_type']) ? $P->vars['oper_type'] : $P->oper_type;

        sValidate::isIntWidth($P->did,10);

        if(!sValidate::$code) {
            switch($P->oper_type) {

                case 'exchange':

                    $demand = Model::Demand('HOME')->getInfo(array('did' => $P->did),'demand');


                    if (!empty($demand)) {
                        //вывод кошелька на кот. будет выполняться перевод
                        $purse = dataBase::DBexchange()->select('balance','purse,desc_val',"where name='".$demand['ex_output']."'");
                        $cur_in_info = dataBase::DBexchange()->select('balance','desc_val',"where name='".$demand["ex_input"]."'");

                        $out_val = $demand['out_val'];
                        //$demand['out_val'] = number_format(round($out_val), 2, '.', ' ');
                        //$demand['in_val'] = number_format($demand['in_val'], 2, '.', ' ');
                        $demand['add_date'] = date('d.m.Y H:i:s',$demand['add_date']);

                        if(!empty($demand["coment"]) && $demand['status'] != 'y') {
                            $demand['comment'] = parent::iterate_tmpl('exchanger',__CLASS__,'comment',array('comment'=>$demand['coment']));
                        }

                        if ($demand["status"] == "n") {

                            $sel_idpay = dataBase::DBadmin()->select('id_payment','id_pay',"where did=".$P->did);

                            if($demand['ex_output'] == "EasyPay") {

                                $purse_out = $demand["purse_out"];
                                $demand['out_val'] = number_format(round($out_val), 0, '.', ' ');

                            } else {
                                $purse_out = $purse[0]['purse'];
                            }

                            $PP = Extension::Payments()->getParam('payments');

                            $submit = Vitalis::tmpl('Widgets')->load_tmpl_block('webmoney.paramerty_payment',array(
                                'output'=>$demand["ex_output"],
                                'input'=>$demand["ex_input"],
                                'in_val'=>$demand["in_val"],
                                'purse_out'=>$purse_out,
                                'purse_in'=>$demand["purse_in"],
                                'out_val'=>$demand["out_val"],
                                'id_pay'=>$sel_idpay[0]["id_pay"],
                                'desc_pay' => swConstructor::descriptionPayment($P->oper_type,$demand),
                                'did'=>$P->did,
                                'type_action'=>'exchange',
                                'url_wm_merchant' => $PP->url_wm_merchant
                            ));

                        }

                        $instruction = $this->load_tmpl_block('info.instruction',array(
                            'oper_type'     => $P->oper_type,
                            'status'        => $demand["status"],
                            'ex_output'     => $demand["ex_output"],
                            'edit_out_val'  => $out_val,
                        ));

                        $demand['status_name'] = swDemand::$status_name[$demand["status"]];
                        $demand['status_class'] = swDemand::$status_class[$demand["status"]];
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'exchange_form',array_merge($demand,array(
                            'purse'=>$purse[0]['purse'],
                            'purse_desc_val' =>$purse[0]['desc_val'],
                            'cur_in_info' => $cur_in_info[0]['desc_val'],
                            'cur_output' => swConstructor::cur_output($demand["ex_output"],$demand["purse_out"]),
                            'cur_input' => swConstructor::cur_input($demand["ex_input"],$demand["purse_in"]),
                            'submit' => $submit,
                            'instruction'=>$instruction
                        )));
                    } else {
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'error',array('error_message'=>$P->vars['L_demand_error']));
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
                                'output'    => $demand_info[0]["output"],
                                'input'     => $demand_info[0]["name_uslugi"],
                                'in_val'    => $demand_info[0]["out_val"],
                                'out_val'   => $demand_info[0]["in_val"],
                                'purse_out' => $purse_out,
                                'purse_in'  => $demand_info[0]["purse_in"],
                                'id_pay'    => $sel_idpay[0]["id_pay"],
                                'desc_pay'  => $desc_pay,
                                'did'       => $P->did,
                                'type_action'=>'oplata'
                            ));
                        }

                        $demand_info[0]['status_name'] = swDemand::$status_name[$demand_info[0]["status"]];
                        $demand_info[0]['status_class'] = swDemand::$status_class[$demand_info[0]["status"]];

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
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'error',array('error_message'=>$P->vars['L_demand_error']));
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

                        $demand_info[0]['status_name'] = swDemand::$status_name[$demand_info[0]["status"]];
                        $demand_info[0]['status_class'] = swDemand::$status_class[$demand_info[0]["status"]];

                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'refill_form',array_merge($demand_info[0],array(
                            'purse'=>$purse[0]['purse'],
                            'purse_desc_val' =>$purse[0]['desc_val'],
                            'cur_in_info' => $cur_in_info[0]['desc_val'],
                            'cur_output' => swConstructor::cur_output($demand_info[0]["ex_output"],$demand_info[0]["purse_out"]),
                            'cur_input' => swConstructor::cur_input($demand_info[0]["ex_input"],$demand_info[0]["purse_in"]),
                            'submit' => $submit,
                            'instruction'=>$instruction
                        )));
                    } else {
                        $this->vars['html'] = parent::iterate_tmpl('exchanger',__CLASS__,'error',array('error_message'=>$P->vars['L_demand_error']));
                    }

                    break;
                case 'shop':

                    break;
            }
        } else {
            $this->vars['html'] = $P->vars['L_demand_error'];
        }



        return $this;
    }

    public function process($P) {

        $status = 0;

        sValidate::isIntWidth($P->did,10,'L_bad_did');

        switch($P->type_action) {

            case 'exchange':

                $demand = dataBase::DBexchange()->select('demand','ex_output,ex_input,out_val,in_val,purse_out,purse_in,purse_payment',"where did={$P->did} and status='n'");

                if(!empty($demand)) {

                    if($P->output_system == 'EasyPay') {

                        $result = Vitalis::Controller('CheckPayment','checkPaymentEasypay',$demand[0],'gc');

                    } elseif($P->output_system == 'WMT') {

                         $signature = Model::Demand()->createSignature(array(
                            'output' => $demand[0]['ex_output'],
                            'input' => $demand[0]['ex_input'],
                            'in_val' => $demand[0]['in_val'],
                            'out_val' => $demand[0]['out_val'],
                            'id_pay' => $P->pay_id,
                            'did' => $P->did
                        ));

                        if($signature == $P->signature) {

                            return $result = Vitalis::Controller('BillPayment','billWM',array_merge((array)$P,array(
                                'type_action'   => 'demand',
								'purse_type' => $demand[0]['ex_output'],
								'amount' => $demand[0]['out_val'],
								'desc' => "Внимание! Если вы не выписывали счет не оплачивайте! Direction of the exchange: {$demand[0]['ex_output']}->{$demand[0]['ex_input']}, ID:{$P->did}",
								'direct' => $demand[0]['ex_output'].'_'.$demand[0]['ex_input']
							)),'gc');

                        } else {
                            $result['status'] = 1; $result['message'] = Config::$sysMessage['L_error_signature'];
                        }

                    }

                } else {
                    $result['status'] = 1; $result['message'] = Config::$sysMessage['L_pay_paid'];
                }

                return json_encode($result);

                break;

			case 'oplata':

			$demand = dataBase::DBpaydesk()->select('demand_uslugi','*',"where did={$P->did} and status='n'");

			if(!empty($demand)) {

				if($P->output_system == 'EasyPay') {

					$curl = Extension::Rest(Config::$base['HOME_URL'].'/api/CheckPayment/checkPaymentEasypay/');
			        $curl->post($demand[0]);
                    $result = $curl->execute();

				} elseif($P->output_system == 'WMT') {

					 $signature = Model::Demand('HOME')->createSignature(array(
						'output' => $demand[0]['output'],
						'input' => $demand[0]['name_uslugi'],
						'in_val' => $demand[0]['out_val'],
						'out_val' => $demand[0]['in_val'],
						'id_pay' => $P->pay_id,
						'did' => $P->did
					));

					if($signature == $P->signature) {

                        $curl = Extension::Rest(Config::$base['HOME_URL'].'/api/BillPayment/billWM/');
                        $curl->post(array(
                            'did'           => $P->did,
                            'pay_id'        => $P->pay_id,
                            'wmid'          => $P->wmid,
                            'type_action'   => $P->type_action,
                            'purse_type'    => $demand[0]['output'],
                            'amount'        => $demand[0]['out_val'],
                            'desc'          => "Payment facilities: {$demand[0]['name_uslugi']}, ID:{$demand[0]['did']}",
                            'direct'        => $demand[0]['output'].'_'.$demand[0]['name_uslugi']
                        ));

                        return $curl->execute();

					} else {
						$result['status'] = 1; $result['message'] = Config::$sysMessage['L_error_signature'];
					}

				}

			} else {
				$result['status'] = 1; $result['message'] = Config::$sysMessage['L_pay_paid'];
			}

			return json_encode($result);

			break;
				
            case 'refill':

                if(!sValidate::$code) {
                    $info = dataBase::DBpaydesk()->select('demand_cash','status','where did='.$P->did);
                    if($info[0]['status'] == 'n') {
                        dataBase::DBpaydesk()->update('demand_cash',array('status'=>'yn'),'where did='.$P->did);
                        $message = $P->vars['L_confirm_ok'];
                    } elseif($info[0]['status'] == 'yn') {
                        $status = 1;
                        $message = $P->vars['L_confirm_process'];
                    } elseif($info[0]['status'] == 'y') {
                        $message = $P->vars['L_confirm_payed'];
                    } else {
                        $status = 1;
                        $message = $P->vars['L_confirm_error'];
                    }

                } else {
                    $status = 1; $message = sValidate::$message;
                }

                break;

        }

        return json_encode(array('status'=>$status,'message'=>$message));
    }
}
