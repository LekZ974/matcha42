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

    public function isLike($id, $id2)
    {
        $like = $this->app->db->prepare("SELECT * FROM likes WHERE id_user = ? AND id_user_like = ?");
        $like->execute([$id, $id2]);
        $fetch = $like->fetchall();
        if (count($fetch) == 1)
            return true;
        return false;
    }

    public function isMatch($id, $id2)
    {
        $like = $this->app->db->prepare("SELECT * FROM likes WHERE (id_user = ? AND id_user_like = ?) OR (id_user = ? AND id_user_like = ?)");
        $like->execute([$id, $id2, $id2, $id]);

        $fetch = $like->fetchall();
        if (count($fetch) == 2)
        {
            $this->update($fetch[0]['id'], ['is_match' => 1]);
            $this->update($fetch[1]['id'], ['is_match' => 1]);

            return true;
        }
        if ($fetch[0]['is_match'] == 1)
            $this->update($fetch[0]['id'], ['is_match' => 0]);

        return false;
    }

    public function deleteLike($id, $likeId)
    {
        $pdo = $this->app->db->prepare("DELETE FROM likes WHERE id_user = ? AND id_user_like = ?");
        $pdo->execute([$id, $likeId]);
    }
}