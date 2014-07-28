<?
class support extends TemplateWidgets {

    public function add($P) {

        return $this->block($P);
    }

    public function block($P) {

        if($P->controller != 'support' || empty($P->object)) {

           $this->vars['email'] = $this->iterate_tmpl('info',__CLASS__,'email');
           $this->vars['owner_id'] = '0';
           $this->vars['action'] = 'add_message';
		   $this->vars['placeholder'] = 'Описывайте подробно Вашу проблему. Если вопрос касается выполнения заявки, указывайте ОБЯЗАТЕЛЬНО ее номер.';

        } else {

            $support = dataBase::DBadmin()->select('support',
                '*',
                "where indeficator='".$P->object."' and upd_date >= DATE_ADD(now(),INTERVAL -3 day)"
            );

			if(!empty($support)) {
	            $support_owner = dataBase::DBadmin()->select('support',
	                '*',
	                "where owner_id=".$support[0]['id'],
	                'order by add_date asc');

	            $this->vars['owner_id'] = $support[0]['id'];
	            $support[0]['who'] = '-user';
	            $support[0]['word'] = $P->vars['L_send_in'];
	            $support[0]['i'] = $P->vars['L_here_mail'];
	            $support[0]['date'] = date('d.m.Y в H:i',$support[0]['add_date']);

	            $this->vars['main_message'] = $this->iterate_tmpl('info',__CLASS__,'iterate',$support[0]);

	            if(!empty($support_owner)) {

	                foreach($support_owner as $sw) {
	                    $sw['date'] = date('d.m.Y в H:i',$sw['add_date']);
	                    $sw['who'] = $sw['author'] == 0 ? '-user' : '-support';
	                    $sw['i'] = $sw['author'] == 0 ? $P->vars['L_here_mail'] : $P->vars['L_answer_mail'];
	                    $sw['word'] = $sw['author'] == 0 ? $P->vars['L_send_in'] : $P->vars['L_send_from'];
	                    $this->vars['html'] .= $this->iterate_tmpl('info',__CLASS__,'iterate',$sw);
	                }
	            }

	            $this->vars['action'] = 'update_message';

			} else {

				$this->tmplName = 'empty';
				$this->vars['email'] = $this->iterate_tmpl('info',__CLASS__,'email');
				$this->vars['owner_id'] = '0';
				$this->vars['action'] = 'add_message';
			}
        }

        return $this;
    }

    public function process($P) {

        $status = 0;

        switch ($P->action) {
            case "add_message":

            sValidate::Email($P->email);
            sValidate::isInt($P->owner_id);
            sValidate::BodyMess($P->message);

            if(!sValidate::$code) {
                $indeficator = strtoupper(substr(md5($P->email.time()),0,16));
                dataBase::DBadmin()->insert('support',array(
                	'email'         => $P->email,
                	'message'       => $P->message,
                	'ip'            => sSystem::getIp('ip'),
                	'proxy'         => sSystem::getIp('proxy'),
                	'add_date'      => time(),
                	'indeficator'   => $indeficator,
                	'upd_date'      => date('Y-m-d H:i:s',time())
                ));

                $message = $P->vars['L_send_ok'];

                $mail = mailSender::init();
                $mail->to = $P->email;
                $mail->subject = "[WM-RB.net] ".$P->vars['L_subject_mail'];
                $mail->message = parent::iterate_tmpl('emails',Config::getLang(),'add_message_support',array(
                    'bottom_support' => parent::iterate_tmpl('emails',Config::getLang(),'bottom_support'),
                    'indeficator' => $indeficator,
                    'url' => Config::$base['STATIC_URL'].'/support/'.$indeficator.'/',
                ));
                $mail->smtpmail();

            } else {
                $status = 1; $message = sValidate::$message;
            }

            break;
            case 'update_message';
                sValidate::isInt($P->owner_id);
                sValidate::BodyMess($P->message);

                if(!sValidate::$code) {
					dataBase::DBadmin()->update('support',array('status'=>0),'where id='.$P->owner_id);
                    dataBase::DBadmin()->insert('support',array(
                    	'message'   => $P->message,
                    	'ip'        => sSystem::getIp('ip'),
                    	'proxy'     => sSystem::getIp('proxy'),
                    	'add_date'  => time(),
                    	'owner_id'  => $P->owner_id
                    ));
                    $message = $P->vars['L_send_ok'];

                    $sw['date'] = date('d.m.Y в H:i',time());
                    $sw['who'] = '-user';
                    $sw['i'] = $P->vars['L_here_mail'];
                    $sw['word'] = $P->vars['L_send_in'];
                    $sw['message'] = $P->message;
                    $html = $this->iterate_tmpl('info',__CLASS__,'iterate',$sw);

                } else {
                    $status = 1; $message = sValidate::$message;
                }
                break;
            case 'enter';

            break;
        }


        return json_encode(array('status'=>$status,'message'=>$message,'html'=>$html,'appendElement'=>'support_content'));
    }
}

?>
