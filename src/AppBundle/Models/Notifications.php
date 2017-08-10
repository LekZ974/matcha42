<?php

namespace App\AppBundle\Models;


use App\AppBundle\Model;
use App\AppBundle\Models\Messages;

class Notifications extends Model
{
    public function sendNotification($type, $id, $destId, $message, $path = null)
    {
        $this->insert([
            'type' => $type,
           'id_user' => $id,
            'id_user_dest' => $destId,
            'message' => $message,
            'link' => $path,
        ]);
        if ($type == 'message')
        {
            $notif = $this->findLast();
            $messages = new Messages($this->app);
            $messages->insert([
                'idNotif' => $notif['id'],
                'id_user' => $id,
                'id_user_dest' => $destId,
                'message' => $message,
            ]);
        }
    }

    public function getNotification($id)
    {
        $notif = $this->app->db->prepare("SELECT *, n.id as idNotif, n.created_at as dateNotif
         FROM notifications n
         LEFT JOIN users u ON u.id = n.id_user
         LEFT JOIN pictures im ON im.id_user = n.id_user AND im.is_profil = 1
         WHERE n.id_user_dest = ?
         ORDER BY n.id DESC
        ");
        $notif->execute([$id]);

        return $notif->fetchAll();
    }

    public function getUnreadNotification($id)
    {
        $notif = $this->app->db->prepare("SELECT *, n.id as idNotif, n.created_at as dateNotif
         FROM notifications n
         LEFT JOIN users u ON u.id = n.id_user
         LEFT JOIN pictures im ON im.id_user = n.id_user AND im.is_profil = 1
         WHERE n.id_user_dest = ? AND n.reading = 0
         ORDER BY n.id DESC LIMIT 8
        ");
        $notif->execute([$id]);

        return $notif->fetchAll();
    }

    public function getCountUnreadNotif($id)
    {

        $notif = $this->app->db->prepare("SELECT *
         FROM notifications n 
         LEFT JOIN users u ON u.id = n.id_user
         LEFT JOIN pictures im ON im.id_user = n.id_user AND im.is_profil = 1
         WHERE n.reading = 0
         AND n.id_user_dest = ?");
        $notif->execute(array($id));

        return count($notif->fetchAll());
    }

    public function setAsRead($id)
    {
        $notif = $this->app->db->prepare("UPDATE notifications SET reading = 1 WHERE id = ?");
        $notif->execute([$id]);
    }

    public function getLastNotification($id, $nb)
    {
        $notif = $this->app->db->prepare("SELECT *, n.id as idNotif, n.created_at as dateNotif
         FROM notifications n
         RIGHT JOIN users u ON u.id = n.id_user
         RIGHT JOIN pictures im ON im.id_user = n.id_user AND im.is_profil = 1
         WHERE n.id_user_dest = ?
         ORDER BY n.created_at DESC LIMIT 0,$nb
        ");
        $notif->execute([$id]);

        return $notif->fetchAll();
    }
}