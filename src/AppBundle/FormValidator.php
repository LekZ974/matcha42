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

    protected $app;

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
                        $this->error[$field][] = "You must have more 18 years old";
                    elseif (strlen($field) > 3)
                        $this->error[$field][] = "Are you not too old for this bullshit? / put a valid age (< 100)";
                    break;
                case 'required':
                    if (!$_POST[$field])
                        $this->error[$field][] = "This field is require";
                    break;
                case 'isMail':
                    if (!filter_var($_POST[$field], FILTER_VALIDATE_EMAIL))
                        $this->error[$field][] = "Mail not valid!";
                    break;
                case 'maxLength':
                    if (strlen($_POST[$field]) > 30)
                        $this->error[$field][] = "This field is too long!! !";
                    break;
                case 'isPassword':
                    $point = $this->testpassword($_POST[$field]);
                    if ($point < 60)
                        $this->error[$field][] = "Password not secure (try with upcase characters, numeric characters and more than 6 characters)";
                    break;
                case 'isSamePassword':
                    if ($_POST[$field] != $_POST['password'])
                        $this->error[$field][] = "Is not match with your password!";
                    break;
                case 'isNumeric';
                    if (!is_numeric($_POST[$field]))
                        $this->error[$field][] = "Not valid";
                    break;
                case 'isSingle':
                    $user = new Users($this->app);
                    if ($user->isSingle($field, $_POST[$field]) == false)
                    {
                        $this->error[$field][] = $_POST[$field] . " is already taking!";
                    }
                    break;
                case 'isExist':
                    $user = new Users($this->app);
                    if ($user->isSingle($field, $_POST[$field]) == true)
                    {
                        $this->error[$field][] = $_POST[$field] . " is not exist!";
                    }
                    break;
            }
        }
    }
}