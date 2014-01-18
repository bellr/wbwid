<?
class menu extends TemplateWidgets {

    public function block() {
        $currency = dataBase::DBexchange()->select('balance','name,desc_val','where st_cash=1');

        foreach($currency as $cur) {
            $cur['type'] = 'refill';
            $this->vars['items'] .= parent::iterate_tmpl('inc',__CLASS__,'item_refill',$cur);
        }

        $currency = dataBase::DBpaydesk()->select('uslugi','name,desc_val','where name_cat="forex" and status=1');
        $instaforex = array('USD','RUR','EUR');
        $aforex = array('USD','RUR');
        foreach($currency as $cur) {

            foreach($$cur['name'] as $c) {
                $sub['direction'] = $cur['name'].'_'.strtolower($c);
                $sub['name'] = $c;
                $cur['sub'] .= parent::iterate_tmpl('inc',__CLASS__,'sub_refill_forex',$sub);
            }

            $this->vars['items_forex'] .= parent::iterate_tmpl('inc',__CLASS__,'item_refill_forex',$cur);
        }

        return $this;
    }
}