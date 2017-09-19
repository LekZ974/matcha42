<?php

namespace App\AppBundle;


class Security
{
    public static function secureDB($string)
    {
        if(ctype_digit($string))
            $string = intval($string);
        else
            $string = addcslashes($string, '%_');

        return $string;

    }

    public static function secureXSS($string)
    {
        return htmlentities($string);
    }

    public static function secureInput($str)
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