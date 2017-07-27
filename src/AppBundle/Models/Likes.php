<?php

namespace App\AppBundle\Models;


use App\AppBundle\Model;

class Likes extends Model
{
    public function likeUser($id, $likeId)
    {
        $like = $this->app->db->prepare("SELECT *
        FROM likes 
        WHERE id_user = ? AND id_user_like = ?");
        $like->execute([$id, $likeId]);

        if (!empty($like->fetchAll()))
            return -1;
        $like = $this->app->db->prepare("SELECT *
        FROM likes 
        WHERE id_user_like = ?");
        $like->execute([$id]);
        $lika = $like->fetchAll();
        if(!empty($lika))
            return $lika['id'];
        return 0;
    }

    public function isMatch($id, $id2)
    {
        $user1 = $this->findOne('id_user', $id);
        $user2 = $this->findOne('id_user', $id2);
        if ($user1['id_user_like'] == $id2 && $user2['id_user_like'] == $id)
            return true;
        return false;
    }

    public function deleteLike($id, $likeId)
    {
        $pdo = $this->app->db->prepare("DELETE FROM likes WHERE id_user = ? AND id_user_like = ?");
        $pdo->execute([$id, $likeId]);
    }
}