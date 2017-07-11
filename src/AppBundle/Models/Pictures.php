<?php
/**
 * Created by PhpStorm.
 * User: ahoareau
 * Date: 7/6/17
 * Time: 10:10 AM
 */

namespace App\AppBundle\Models;

use App\AppBundle\Model;

class Pictures extends Model
{
    public function getProfilPic($id)
    {
        $img = $this->app->db->prepare("SELECT * FROM pictures WHERE id_user = ? AND is_profil = 1");
        $img->execute([$id]);
        $img = $img->fetch();
        return $img['url'];
    }
    public function getImagesByIdUser($id)
    {
        $img = $this->app->db->prepare("SELECT * FROM pictures WHERE id_user = ?");
        $img->execute([$id]);
        return $img->fetchAll();
    }
    public function setAsDefault($id, $idUsers)
    {
        $us = $this->app->db->prepare("UPDATE pictures SET isprofil = 0 WHERE id_users = ? ");
        $us->execute([$idUsers]);
        $us = $this->app->db->prepare("UPDATE pictures SET isprofil = 1 WHERE id = ? ");
        $us->execute([$id]);
    }
    public function isProfil($id)
    {
        $img = $this->findOne('id', $id);
        if ($img['is_profil'] == '1')
            return true;
        return false;
    }
    public function deleteImage($id, $idUser)
    {
        $isprofil = $this->isProfil($id);
        $us = $this->app->db->prepare("DELETE FROM pictures WHERE id = ? ");
        if ($us->execute([$id]))
        {
            if ($isprofil)
            {
                $us = $this->findOne('id_user', $idUser);
                if (!empty($us))
                {
                    $ide = $us['id'];
                    $us = $this->app->db->prepare("UPDATE pictures  SET is_profil = 1 WHERE id = ? ");
                    $us->execute(array($ide));
                }
            }
            return true;
        }
        return false;
    }

    public function getImageURL($id)
    {
        $img = $this->app->db->prepare("SELECT * FROM pictures WHERE url = ? ");
        $img->execute([$url]);
        $img = $img->fetch();
        return $img['id'];
    }

    public function getImageIdByURL($url)
    {
        $img = $this->app->db->prepare("SELECT * FROM pictures WHERE url = ? ");
        $img->execute([$url]);
        $img = $img->fetch();
        return $img['id'];
    }
}