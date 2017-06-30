<?php

namespace App\AppBundle;

use App\AppBundle\Models\Users;

/**
 * @author Alexandre Hoareau <ahoareau@student.42.fr>
 */
class FormValidator
{
    public $form;

    public $error;

    private $app;

    public function __construct($form, $c)
    {
        $this->form = $form;
        $this->app = $c;
    }

    public function testpassword($mdp)
    {
        $lengthPassword = strlen($mdp);
        if($lengthPassword == 0)
            return 0;

        $point = null;
        $scoreLowerCase = null;
        $scoreSpecial = null;
        $scoreDigit = null;
        $scoreUpperCase = null;

        for ($i = 0; $i < $lengthPassword; $i++)
        {
            $indexPassword = $mdp[$i];
            if ($indexPassword >= 'a' && $indexPassword <= 'z')
            {
                $point = $point + 1;
                $scoreLowerCase = 1;
            } else if ($indexPassword >= 'A' && $indexPassword <= 'Z')
            {
                $point = $point + 2;
                $scoreUpperCase = 2;
            } else if ($indexPassword >= '0' && $indexPassword <= '9')
            {
                $point = $point + 3;
                $scoreDigit = 3;
            } else
            {
                $point = $point + 5;
                $scoreSpecial = 5;
            }
        }

        return ($point / $lengthPassword) * ($scoreLowerCase + $scoreUpperCase + $scoreDigit + $scoreSpecial) * $lengthPassword;
    }

    public function check($field, $conditions)
    {
        foreach ($conditions as $value)
        {
            switch ($value)
            {
                case 'age';
                    if($_POST[$field] < 18)
                        $this->error[$field][] = "Tu dois être majeur pour t'inscrire";
                    elseif (strlen($field) > 3)
                        $this->error[$field][] = "T'es pas un peu trop vieux pour ces conneries? / mets un âge valide";
                    break;
                case 'required':
                    if (!$_POST[$field])
                        $this->error[$field][] = "Ce champs est requis";
                    break;
                case 'isMail':
                    if (!filter_var($_POST[$field], FILTER_VALIDATE_EMAIL))
                        $this->error[$field][] = "Mail non correct";
                    break;
                case 'maxLength':
                    if (strlen($_POST[$field]) > 30)
                        $this->error[$field][] = "Ce champs est trop longs, voyons !";
                    break;
                case 'isPassword':
                    $point = $this->testpassword($_POST[$field]);
                    if ($point < 60)
                        $this->error[$field][] = "Mot de passe non sécurisée (Essayez avec une majuscule, des chiffres et + de six caracteres)";
                    break;
                case 'isNumeric';
                    if (!is_numeric($_POST[$field]))
                        $this->error[$field][] = "Pas un age valide";
                    break;
                case 'isSingle':
                    $user = new Users($this->app);
                    if ($user->isSingle($field, $_POST[$field]) == false)
                    {
                        $this->error[$field][] = "Email déja pris";
                    }
                    break;
            }
        }
    }
}