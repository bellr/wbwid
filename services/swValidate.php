<?
class swValidate extends sValidate {

    protected static function Check_Balance($arg) {

        if($arg[0] != 'usluga') {
            $balance = Model::Balance('HOME')->getBalanceForPay($arg[0],array('currency'=>$arg[2]));
        } else {
            $balance = Model::Balance('HOME')->getPurseService();
        }

        if($balance < $arg[1]) {self::$code = 1; self::$message = Config::$sysMessage['L_error_selected_balance'];}
    }





}
