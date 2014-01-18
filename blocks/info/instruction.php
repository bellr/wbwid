<?
class instruction extends TemplateWidgets {

    public function block($P) {

        switch($P->vars['oper_type']) {

            case 'exchange':
                if ($P->vars['status'] == "n") {
                    if ($P->vars['ex_output'] == "EasyPay") {
                        $EP_purse = Model::Acount_easypay()->getPurseInput($P->vars['edit_out_val'],'desc');
                        dataBase::DBexchange()->update('demand',array('purse_payment'=>$EP_purse),array(
                            'did' => $P->did,
                            'status' => 'n'
                        ));

                        if(!empty($EP_purse)) {
                            $instruction_tmpl = $P->vars['ex_output'];
                       } else {
                            $instruction_tmpl = 'empty';
                       }
;
                        $this->vars['instruction'] = $this->iterate_tmpl('info',__CLASS__,$instruction_tmpl,array(
                            'edit_out_val'  => number_format(round($P->vars['edit_out_val']), 0, '.', ' '),
                            'EP_purse'      => $EP_purse,
                            'did'           => $P->did
                        ));
                    }
                    elseif ($P->vars['ex_output'] == "YaDengi") {
                        echo "&bull; Оплачивайте указанную сумму на счет <b>Yandex.Money {purse}</b>, в примечании к платежу обязательно указать номер заявки <b>{$P->did}</b>, после оплаты сообщить через <a href=\"javascript:show_hide(1)\"><b>форму</b></a> или на странице \"<a href=\"http://wm-rb.net/user_check_demand.aspx\"><b>Проверка заявки</b></a>\"<br />";
                    }
                    elseif ($P->vars['ex_output'] == "RBK Money"){
                        echo "&bull; Оплачивайте указанную сумму на счет <b>RBK Money {purse}</b>, в примечании к платежу обязательно указать номер заявки <b>{$P->did}</b>, после оплаты сообщить через <a href=\"javascript:show_hide(1)\"><b>форму</b></a> или на странице \"<a href=\"http://wm-rb.net/user_check_demand.aspx\"><b>Проверка заявки</b></a>\"<br />";
                    }
                }
            break;

            case 'refill':
                if ($P->vars['status'] == "n") {
                    $EP_purse = Model::Acount_easypay()->getPurseInputUnlimit($P->vars['edit_out_val'],'desc');
                    dataBase::DBpaydesk()->update('demand_cash',array('purse_payment'=>$EP_purse),'where did='.$P->did.' and status="n"');

                    $this->vars['instruction'] = parent::iterate_tmpl('info',__CLASS__,'refill',array(
                        'edit_out_val'=>number_format($P->vars['edit_out_val'], 0, '.', ' '),
                        'EP_purse'=>$EP_purse,
                        'did'=>$P->did
                    ));
                }
            break;
        }

        return $this;
    }
}

?>