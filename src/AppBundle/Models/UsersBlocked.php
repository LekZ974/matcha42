<?php

namespace App\AppBundle\Models;


use App\AppBundle\Model;

class UsersBlocked extends Model
{
    public function isBlocked($id, $id2)
    {
        $blocked = $this->app->db->prepare("SELECT DISTINCT * FROM usersblocked WHERE (id_user = ? AND id_user_blocked = ?) OR (id_user = ? AND id_user_blocked = ?)");
        $blocked->execute([$id, $id2, $id2, $id]);

        $fetch = $blocked->fetch();
        print_r(count($fetch));
        if (count($fetch) >= 1 && !empty($fetch))
        {
            return true;
        }

        return false;
    }

}