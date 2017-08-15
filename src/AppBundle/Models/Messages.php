<?php

namespace App\AppBundle\Models;


use App\AppBundle\Model;

class Messages extends Model
{
    public function getLastMessage($id, $id2)
    {
        $messages = $this->app->db->prepare("SELECT u.id, m.message, n.reading, m.id_user as idAuth, m.id as idMessage, n.id as idNotif, m.created_at as dateNotif
         FROM messages m
         RIGHT JOIN users u ON u.id = m.id_user
         RIGHT JOIN notifications n ON n.id_user = m.id_user AND n.id = m.idNotif
         WHERE (m.id_user = ? AND m.id_user_dest = ?) OR (m.id_user = ? AND m.id_user_dest = ?)
         ORDER BY dateNotif DESC
        ");
        $messages->execute([$id, $id2, $id2, $id]);

        $lastMessage = $messages->fetch();
        if (!empty($lastMessage))
            return $lastMessage;
        return [];
    }

    public function getMessages($id, $id2)
    {
        $notif = $this->app->db->prepare("SELECT u.id as id_user, m.message, im.url, im.is_profil, m.id as idMessage, m.created_at as dateNotif
         FROM messages m
         RIGHT JOIN users u ON u.id = m.id_user
         RIGHT JOIN pictures im ON im.id_user = m.id_user AND im.is_profil = 1
         WHERE (m.id_user = ? AND m.id_user_dest = ?) OR (m.id_user = ? AND m.id_user_dest = ?)
         ORDER BY dateNotif DESC
        ");
        $notif->execute([$id, $id2, $id2, $id]);

        return $notif->fetchAll();
    }
}