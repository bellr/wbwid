<?
class swConstructor {

	public static function __callStatic($name, $arguments) {

		$func = 'go_'.$name;
		Config::getInfoText(); //return static param Config::$infoText[]
		return static::$func($arguments);
    }
	
    public static function go_name_oper($disp_var,$name_usluga) {
        switch ($disp_var) :
            case ("NAL") :
        $result = '<b>'.Config::$infoText['L_cash_replen'].'</b>';
            break;
            case ("output_NAL") :
        $result = '<b>'.Config::$infoText['L_replen_card'].' {$name_usluga}</b>';
            break;
            case ("uslugi") :
        $result = '<b>'.Config::$infoText['L_pay_services'].' :</b> {$name_usluga}';
            break; endswitch;
        return $result;
    }

    public static function go_output($disp_var,$oplata) {
        switch ($disp_var) :
            case ("NAL") :
        $result = "BLR";
            break;
            case ("output_NAL") :
        $result = $oplata;
            break;
            case ("uslugi") :
        $result = $oplata;
            break; endswitch;
        return $result;
    }

    public static function go_input($disp_var,$oplata) {
        switch ($disp_var) :
            case ("NAL") :
        $result = $oplata;
            break;
            case ("output_NAL") :
        $result = "BLR";
            break;
            case ("uslugi") :
        $result = "BLR";
            break; endswitch;
        return $result;
    }

    public static function go_bal($disp_var,$oplata) {
        switch ($disp_var) :
            case ("NAL") :
        $result = $oplata;
            break;
            case ("output_NAL") :
        $result = $oplata;
            break;
            case ("uslugi") :
        $result = $oplata;
            break; endswitch;
    return $result;
    }

    public static function go_kurs_n($disp_var,$oplata) {
        switch ($disp_var) :
            case ("NAL") :
        $result = "NAL_{$oplata}";
            break;
            case ("output_NAL") :
        $result = "{$oplata}_NAL";
            break;
            case ("uslugi") :
        $result = "{$oplata}_usluga";
            break; endswitch;
        return $result;
    }
    
    //Название формы отправки в cheak_demand
    private static function go_name_submit($disp_var) {
        switch ($disp_var[0]) :
            case ("NAL") :
        $result = "NAL";
            break;
            case ("output_NAL") :
        $result = "output_NAL";
            break;
            case ("uslugi") :
        $result = "usluga";
            break;
        endswitch;

        return $result;
    }

public static function cur_output($output,$purse_out) {
    switch ($output) :
        case ("RBK Money") :
            $html = "
                <tr>
                <td align=\"right\"><b>Ваш RBK Money счет :</b>&nbsp;<br>
                </td>
                <td align=\"left\">{$purse_out}
                </td>
                </tr>";
            break;
        case ("EasyPay") :
            $html =  "
                <tr>
                <td align=\"right\"><b>Ваш EasyPay счет :</b>&nbsp;<br>
                </td>
                <td align=\"left\">{$purse_out}
                </td>
                </tr>";
            break;
        case ("YaDengi") :
            $html = "
                <tr>
                <td align=\"right\"><b>Ваш Yandex.Money счет :</b>&nbsp;<br>
                </td>
                <td align=\"left\">{$purse_out}
                </td>
                </tr>";
            break;
        default:
        endswitch;
    return $html;
}

public static function cur_input($input,$purse_in) {
        switch ($input) :

            case ("WMZ") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>Ваш Z-кошелек :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
                break;
            case ("WMR") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>R-кошелек :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
                break;
            case ("WME") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>E-кошелек :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
                break;
            case ("WMG") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>G-кошелек :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
                break;
            case ("WMU") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>U-кошелек :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
                break;
            case ("WMY") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>Y-кошелек :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
                break;
            case ("WMB") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>B-кошелек :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
                break;
            case ("RBK Money") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>Ваш RBK Money E-mail :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
                break;
            case ("EasyPay") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>Ваш EasyPay счет :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
                break;
            case ("YaDengi") :
                $html = "
                    <tr>
                    <td align=\"right\"><b>Ваш Yandex.Money счет :</b>&nbsp;<br>
                    </td>
                    <td align=\"left\">{$purse_in}
                    </td>
                    </tr>";
            break;
            default:
        endswitch;
    return $html;
}


    public static function check($output,$input) {
        $output_cook = Cookies::get('p_out_'.$output);
        $input_cook = Cookies::get('p_in_'.$input);
        $wmid = Cookies::get('wmid');
        switch ($output) :
            case ("RBK Money") :
                $html = "<td align=\"right\">RBK Money E-mail :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_output\" id=\"p_output\" value=\"{$output_cook}\" size=\"17\">";
                break;
            case ("EasyPay") :
                $html = "<td align=\"right\">Ваш EasyPay счет :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_output\" id=\"p_output\" maxlength=\"8\" value=\"{$output_cook}\" size=\"14\">";
                break;
            case ("YaDengi") :
                $html = "<td align=\"right\">Ваш Yandex.Money счет :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_output\" id=\"p_output\" maxlength=\"14\" value=\"{$output_cook}\" size=\"17\">";
                break;
            default:
        endswitch;

        switch ($input) :
            case ("WMZ") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
<td align=\"right\">Ваш Z-кошелек :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"13\" size=\"14\" value=\"{$input_cook}\"></td>
</tr>";
                break;
            case ("WMR") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">R-кошелек :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"13\" size=\"14\" value=\"{$input_cook}\"></td>
</tr>";
                break;
            case ("WME") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">E-кошелек :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"13\" size=\"14\" value=\"{$input_cook}\"></td>
</tr>";
                break;
            case ("WMG") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">G-кошелек :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"13\" size=\"14\" value=\"{$input_cook}\"></td>
</tr>";
                break;
            case ("WMU") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">U-кошелек :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"13\" size=\"14\" value=\"{$input_cook}\"></td>
</tr>";
                break;
            case ("WMY") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">Y-кошелек :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"13\" size=\"14\" value=\"{$input_cook}\"></td>
</tr>";
                break;
            case ("WMB") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">B-кошелек :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"13\" size=\"14\" value=\"{$input_cook}\"></td>
</tr>";
                break;
            case ("RBK Money") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">RBK Money E-mail :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" value=\"{$input_cook}\" size=\"17\"></td>
</tr>";
                break;
            case ("EasyPay") :
                $html = "
<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">Ваш WMID :&nbsp;<br>
</td>
<td align=\"left\">
	<input type=\"text\" name=\"wmid\" id=\"wmid\" maxlength=\"12\" value=\"{$wmid}\" size=\"14\">
</td>
</tr>
<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">Ваш EasyPay счет :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" value=\"{$input_cook}\" maxlength=\"8\" size=\"14\"></td>
</tr>";
                break;
            case ("YaDengi") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
	<td align=\"right\">Ваш Yandex.Money счет :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" value=\"{$input_cook}\" maxlength=\"14\" size=\"17\"></td>
</tr>";
                break;
            case ("Z-PAYMENT") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
<td align=\"right\">Ваш ZP-кошелек :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"10\" size=\"11\" value=\"{$input_cook}\" ></td>
</tr>";
                break;

            case ("instaforex") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
<td align=\"right\">Номер депозита :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"10\" size=\"11\" value=\"{$input_cook}\" ></td>
</tr>";
                break;
            case ("aforex") :
                $html = "<tr class=\"text\" bgColor=\"#ffffff\">
<td align=\"right\">Номер депозита :&nbsp;<br>
</td>
<td align=\"left\">
<input type=\"text\" name=\"p_input\" id=\"p_input\" maxlength=\"10\" size=\"11\" value=\"{$input_cook}\" ></td>
</tr>";
                break;
            default:
        endswitch;
    return $html;
    }

    static function demandSevices($name_uslugi) {
        if ($name_uslugi == "BF" || $name_uslugi == "BF15" || $name_uslugi == "BF16" || $name_uslugi == "BF17" || $name_uslugi == "BF21" || $name_uslugi == "BF22" || $name_uslugi == "BF23") {$name_uslugi = "ByFlyBel";}
        if ($name_uslugi == "TEL" || $name_uslugi == "TEL17" || $name_uslugi == "TEL15" || $name_uslugi == "TEL16" || $name_uslugi == "TEL21" || $name_uslugi == "TEL22" || $name_uslugi == "TEL022" || $name_uslugi == "TEL23") {$name_uslugi = "Beltelecom";}
        switch ($name_uslugi) :
            case ("ATLANT") :
                $html = "Номер лиц. счета :";	break;
            case ("Beltelecom") :
                $html = "Номер телефона :";	break;
            case ("ByFlyBel") :
                $html = "Номер договора ByFly : ";	break;
            case ("BIN") :
                $html = "Номер контракта : ";	break;
            case ("DSET") :
                $html = "Номер контракта : ";	break;
            case ("SOLO") :
                $html = "Номер договора : ";	break;
            case ("TcmBY") :
                $html = "Номер телефона : ";	break;
            case ("NetSys") :
                $html = "Уникальный индефикатор (UID) : ";	break;
            case ("AchinaPlus") :
                $html = "Номер лиц. счета : ";	break;
            case ("ShaparkiDamavik") :
                $html = "Номер лиц. счета : ";	break;
            case ("ANITEX") :
                $html = "Номер контракта : ";	break;
            case ("IPT") :
                $html = "Номер счета : ";	break;
            case ("Beros") :
                $html = "Номер лиц. счета : ";	break;
            case ("STStelekom") :
                $html = "Номер контракта : ";	break;
            case ("SFERATV") :
                $html = "Номер контракта : ";	break;
            case ("SERVER") :
                $html = "Логин : ";	break;
            case ("NTPP") :
                $html = "Номер лицевого счета : ";	break;
            case ("NTTO") :
                $html = "Номер лицевого счета : ";	break;
            case ("NTTVAP") :
                $html = "Номер лицевого счета : ";	break;
            case ("NTTVPP") :
                $html = "Номер лицевого счета : ";	break;
            case ("NOSTRA") :
                $html = "Номер лицевого счета : ";	break;
            case ("MTIS") :
                $html = "Номер договора : ";	break;
            case ("GLEL") :
                $html = "Номер лицевого счета : ";	break;
            case ("ALTOLAN") :
                $html = "Номер договора : ";	break;
            case ("VELCOM") :
                $html = "Номер телефона : ";	break;
            case ("MTS") :
                $html = "Номер телефона : ";	break;
            case ("LIFE") :
                $html = "Номер телефона : ";	break;
            case ("Dialog") :
                $html = "Номер телефона : ";	break;
            case ("KTV") :
                $html = "Номер контракта : ";	break;
            case ("TVSAT") :
                $html = "Номер договора : ";	break;
            case ("TMEDIA21") :
                $html = "Номер лицевого счета : ";	break;
            case ("ZKH") :
                $html = "Номер л/с : ";	break;
            case ("VODA") :
                $html = "Номер л/с : ";	break;
            case ("GAZ") :
                $html = "Номер л/с : ";	break;
            case ("ZKH16") :
                $html = "Номер л/с : ";	break;
            case ("ZKH21") :
                $html = "Номер л/с : ";	break;
            case ("ZKH22") :
                $html = "Номер л/с : ";	break;
            case ("Megashare") :
                $html = "Номер заказа : ";	break;
            case ("vkontakte") :
                $html = "ID контакта : ";	break;
            default:
        endswitch;
        return $html;
    }

}
