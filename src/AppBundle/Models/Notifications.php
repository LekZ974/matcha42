<?php

namespace App\AppBundle\Models;


use App\AppBundle\Model;

class Notifications extends Model
{
    public function sendNotification($type, $id, $destId, $message, $path = null)
    {
        $notif = $this->insert([
            'type' => $type,
           'id_user' => $id,
            'id_user_dest' => $destId,
            'message' => $message,
            'link' => $path,
        ]);
    }

    public function getNotification($id)
    {
        $notif = $this->app->db->prepare("SELECT *, n.id as idNotif
         FROM notifications n
         LEFT JOIN users u ON u.id = n.id_user
         LEFT JOIN pictures im ON im.id_user = n.id_user AND im.is_profil = 1
         WHERE n.id_user_dest = $id
         ORDER BY n.id DESC
        ");
        $notif->execute();

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
}