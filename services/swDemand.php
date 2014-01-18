<?
class swDemand {

    public static $status_name = array('er'=>'ОШИБКА','n'=>'Не оплачена','p'=>'Счет выписан','yp'=>'Оплачена по счету','yn'=>'Оплачена','y'=>'Выполнена');
    public static $status_class = array('er'=>'red','n'=>'silver','p'=>'orange','yp'=>'blue','yn'=>'blue','y'=>'green');

    public static function wm_ReqID(){
        $time=microtime();
        $int=substr($time,11);
        $flo=substr($time,2,3);
        $f=substr($int,0,7);
        return $f.$flo;
    }

    public static function getStatusList($status) {
        foreach(self::$status_name as $k=>$ar) {
            $selected = $k == $status ? ' selected="selected"' : '';
            $html .= '<option value="'.$k.'"'.$selected.'>'.$ar.'</option>';
        }

        return $html;
    }

}
