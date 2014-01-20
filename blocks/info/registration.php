<?
class registration extends TemplateWidgets {

    public function block($P) {

        $this->vars['action'] = __CLASS__;

        if(!empty($P->partner_id)) {
            Cookies::set('partner_id',$P->partner_id);

            dataBase::DBmain()->query('partner',
                "update partner set refer=refer+1 where id={$P->partner_id} and status=1"
            );
           // $sql = "select id from ip_partner_refer where partner_id='$id' and addr_remote='$ip' and proxy='$proxy'";
           // $db->summ_refer($_GET['partner_id']);
           // $sql = "update partner set refer=refer+1 where id='$id' and status='1'";

            //echo $_SERVER['SERVER_ADDR'];
            /*switch ($_GET['dir']) :
                case ("exchange") : header("location: http://wm-rb.net/".$_GET['dir'].".aspx"); exit(); break;
                case ("output_nal") : header("location: http://wm-rb.net/".$_GET['dir'].".aspx"); exit(); break;
                case ("services_list") : header("location: http://service.wm-rb.net/index.aspx"); exit(); break;
            endswitch;*/

            //header("Location: ".Config::$base['HOME_URL']);
            //exit;
            Browser::go();
        }

        return $this;
    }

    public function process($P) {

        $status = 0;

        switch ($P->action) {

            case "addpartner":

                $P->host = strtolower($P->host);
                sValidate::Email($P->email);
                sValidate::Password($P->pass);
                sValidate::Password($P->repass);
                sValidate::isCompare($P->pass,$P->repass);
                //sValidate::Url($P->host);

                if(!sValidate::$code) {

                    $dubl = dataBase::DBmain()->select('partner',
                        'email, host',
                        "where email='".$P->email."' or host='".$P->host."'");

                    if(!empty($dubl)) {
                        foreach($dubl as $d) {
                            if($d['email'] == $P->email) {$status = 1; $message = $P->vars['L_dubl_email'];}
                            if($d['host'] == $P->host) {$status = 1; $message = $P->vars['L_dubl_host'];}
                        }
                    }

                    if(!$status) {
                        $p = explode("@",$P->email);
                        dataBase::DBmain()->insert('partner',array('email'=>$P->email,'password'=>md5($P->pass),'username'=>$p[0],'host'=>$P->host));

                        $mail = mailSender::init();
                        $mail->to = $P->email;
                        $mail->subject = "[WM-RB.net] ".$P->vars['L_mail_reg'];
                        $mail->message = parent::iterate_tmpl('emails',Config::getLang(),'add_partner',array(
                            'username' => $p[0],
                            'bottom_support' => parent::iterate_tmpl('emails',Config::getLang(),'bottom_support'),
                            'email' => $P->email,
                            'pass' => $P->pass,
                            'host' => $P->host,
                        ));
                        $mail->smtpmail();
                        $status = 0; $message = $P->vars['L_reg_ok'];
                    }
                } else {
                    $status = 1; $message = sValidate::$message;
                }

            break;
            case 'mess_pass';
                sValidate::Email($P->email);
                $p = explode("@",$P->email);

                if(!sValidate::$code) {
                    $dubl = dataBase::DBmain()->select('partner',
                        'id',
                        "where email='".$P->email."'");
                    if(!empty($dubl)) {
                        $pass = substr(swDemand::wm_ReqID(),4,10);
                        dataBase::DBmain()->update('partner',array('password'=>md5($pass)),"where email='{$P->email}'");

                        $mail = mailSender::init();
                        $mail->to = $P->email;
                        $mail->subject = "[WM-RB.net] ".$P->vars['L_mail_repass'];
                        $mail->message = parent::iterate_tmpl('emails',Config::getLang(),'re_pass',array(
                            'username' => $p[0],
                            'bottom_support' => parent::iterate_tmpl('emails',Config::getLang(),'bottom_support'),
                            'pass' => $pass
                        ));
                        $mail->smtpmail();

                        $message = $P->vars['L_send_pass'];
                    } else {
                        $status = 1; $message = $P->vars['L_no_partner'];
                    }

                } else {
                    $status = 1; $message = sValidate::$message;
                }
                break;
            case 'enter';
                sValidate::Email($P->email);
                sValidate::Password($P->passw);

                if(!sValidate::$code) {
                    $isset = dataBase::DBmain()->select('partner',
                        'id,email',
                        "where email='".$P->email."' and password='".md5($P->passw)."'");

                    if(!empty($isset)) {
                        Session::set('id',$isset[0]['id']);
                        Session::set('pass',$P->passw);
                        Session::set('email',$isset[0]['email']);

                    } else {
                        $status = 1; $message = $P->vars['L_error_enter'];
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