<?php

namespace App\AppBundle;


class Security
{
    public static function secureDB($string = null)
    {
        if(ctype_digit($string))
            $string = intval($string);
        else
            $string = addcslashes($string, '%_');

        return $string;

    }

    public static function secureXSS($string = null)
    {
        return htmlentities($string);
    }

    public static function secureInput($str = null)
    {
        if (preg_match("#[<>/'\\\"]#", $str) === 1)
        {
            return preg_replace("#[<>/'\\\"]#", '#', $str);
        }
        return $str;
    }
}
?>

}