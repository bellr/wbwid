<?
class instruction extends Template {
    function __construct($action_method,$vars=array()) {
        $this->$action_method($vars);
    }

    private function block($vars) {
        $P = inputData::init();

        switch($vars['oper_type']) {

            case 'exchange':
                if ($vars['status'] == "n") {
                    if ($vars['ex_output'] == "EasyPay") {
                        $EP_purse = Model::EasyPay()->getPurseInput($vars['edit_out_val']);
                        dataBase::DBexchange()->update('demand',array('purse_payment'=>$EP_purse),'where did='.$P->did.' and status="n"');

                        $this->vars['instruction'] = parent::iterate_tmpl('info',__CLASS__,$vars['ex_output'],array(
                            'edit_out_val'=>number_format($vars['edit_out_val'], 0, '.', ' '),
                            'EP_purse'=>$EP_purse,
                            'did'=>$P->did
                        ));
                    }
                    elseif ($vars['ex_output'] == "YaDengi") {
                        echo "&bull; Оплачивайте указанную сумму на счет <b>Yandex.Money {purse}</b>, в примечании к платежу обязательно указать номер заявки <b>{$P->did}</b>, после оплаты сообщить через <a href=\"javascript:show_hide(1)\"><b>форму</b></a> или на странице \"<a href=\"http://wm-rb.net/user_check_demand.aspx\"><b>Проверка заявки</b></a>\"<br />";
                    }
                    elseif ($vars['ex_output'] == "RBK Money"){
                        echo "&bull; Оплачивайте указанную сумму на счет <b>RBK Money {purse}</b>, в примечании к платежу обязательно указать номер заявки <b>{$P->did}</b>, после оплаты сообщить через <a href=\"javascript:show_hide(1)\"><b>форму</b></a> или на странице \"<a href=\"http://wm-rb.net/user_check_demand.aspx\"><b>Проверка заявки</b></a>\"<br />";
                    }
                }
            break;
            case 'refill':
                if ($vars['status'] == "n") {
                    $EP_purse = Model::EasyPay()->getPurseInputUnlimit($vars['edit_out_val']);
                    dataBase::DBpaydesk()->update('demand_cash',array('purse_payment'=>$EP_purse),'where did='.$P->did.' and status="n"');

                    $this->vars['instruction'] = parent::iterate_tmpl('info',__CLASS__,'refill',array(
                        'edit_out_val'=>number_format($vars['edit_out_val'], 0, '.', ' '),
                        'EP_purse'=>$EP_purse,
                        'did'=>$P->did
                    ));
                }
            break;
        }

        return $this->vars;
    }
}

?>