<?php

namespace App\AppBundle\Models;


use App\AppBundle\Model;

class IpLocation extends Model
{
    public function locateIp($ipUser)
    {
        $ip = $this->app->db->prepare("SELECT * FROM iplocation WHERE ? BETWEEN ip_from AND ip_to");
        $ip->execute([$ipUser]);

        return $ip->fetch();
    }
}