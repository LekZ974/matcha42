<?php

namespace App\AppBundle;


use App\AppBundle\Models\Users;

class IsConnected
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function connect($idUser)
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user']))
        {
            $user = new Users($this->app);
            $user->updatedLogin($idUser, 1);
        }
    }

    public function alreadyConnect($idUser)
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user']))
        {
            $user = new Users($this->app);
            $lastActivity = trim($user->findOne('id', $idUser)['last_seen']);
            $now = new \DateTime('now');
            if (!$lastActivity)
                return false;
            $lastActivity = \DateTime::createFromFormat('d/m/Y H:i:s', $lastActivity);
            $diff = $lastActivity->diff($now);
//            1mn for test
            if ($diff->format('%i') >= 10)
            {
                return $lastActivity->format('d/m/Y H:i:s');
            }
            else
                false;
        }
        return false;
    }

    public function isDisconnected($idUser)
    {
        $user = new Users($this->app);
        $user->setDisconnected($idUser);
        if ($_SESSION['user']['id'] == $idUser)
            $_SESSION = [];
    }

}