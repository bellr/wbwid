<?
class footer extends TemplateWidgets {

    public function block() {

        $PP = Extension::Payments()->getParam('payments','webmoney');

        $default_wmid = $PP->default_wmid;
		$this->vars['date'] = date('Y');
        $this->vars['wmid'] = $PP->$default_wmid;

        return $this;
    }
}
