<?php

namespace App\AppBundle\Models;

use App\AppBundle\Model;

class Users extends Model
{
    public function getHome($id = null)
    {
        $us = $this->app->db->prepare("SELECT u.name, u.lastname, u.interests, u.id AS id_user, img.url, img.is_profil
                    FROM users u
                    LEFT JOIN pictures img ON img.id_user = u.id
                    WHERE u.id != ?
                    ORDER BY u.popularity DESC
                    LIMIT 8");
        $us->execute([$id]);

        return $us->fetchAll();
    }

    public function getUserData($id)
    {
        $us = $this->app->db->prepare("SELECT u.name, u.lastname, u.age, u.interests, u.resume, u.gender, u.orientation, u.is_connected, u.id AS id_user, img.id, img.url, img.is_profil
                    FROM users u
                    LEFT JOIN pictures img ON img.id_user = u.id
                    WHERE u.id = ?");
        $us->execute([$id]);

        return $us->fetchAll();
    }

    public function updatedLogin($id)
    {
        $date = date("d/m/Y H:i:s");
        $us = $this->app->db->prepare("UPDATE Users SET last_seen = ?, isConnected = ? WHERE id = ?");
        $us->execute([$date, 1, $id]);
    }

    public function setSaltForget($id)
    {
        $salt = uniqid();
        $this->update($id, array('salt' => $salt));

        return $salt;
    }

    public function getLocationById($id)
    {
        $loca = $this->app->db->prepare("SELECT * FROM usersLocation WHERE id_users = ?");
        $loca->execute(array($id));
        return $loca->fetch();
    }

    public function getAllUser()
    {
        $pdo = $this->app->db->prepare("SELECT u.*, ui.url, ul.city, ul.longitude, ul.latitude FROM Users u INNER JOIN usersImage ui ON ui.id_users = u.id AND ui.isprofil = 1 INNER JOIN usersLocation ul ON ul.id_users = u.id");
        $pdo->execute();
        return $pdo->fetchAll();
    }

    public function checkPass($id, $old, $pass1, $pass2)
    {
        $user = $this->getUsers($id);

        if(hash('whirlpool', $old) != $user['password'])
            return -1;
        else if ($pass1 != $pass2)
            return -2;
        else
            $this->update($id, array('password' => hash('whirlpool', $pass1)));
        return 1;
    }

    public function changePass($id, $salt, $pass)
    {
        $user = $this->getUsers($id);
        if ($user['salt'] == $salt)
        {
            $this->update($id, array('password' => hash('whirlpool', $pass),
                'salt'   => uniqid()));
            return true;
        }
        return false;
    }

    public function isGoodSalt($mail, $salt)
    {
        $sal = $this->app->db->prepare("SELECT * FROM Users WHERE salt = :salt AND mail = :mail");
        $sal->execute(array('salt' => $salt, 'mail' => $mail));

        if (empty($sal->fetch()))
            return false;

        return true;
    }

    public function setDisconnected($id)
    {
        $date = date("d/m/Y H:i:s");
        $us = $this->app->db->prepare("UPDATE Users SET last_seen = ?, isConnected = ? WHERE id = ?");
        $us->execute(array($date, 0, $id));
    }

    public function checkLog($value)
    {
        $pdo = $this->app->db->prepare("SELECT * FROM users WHERE mail = ? AND password = ?");
        $pdo->execute([$value['mail'], hash('whirlpool', $value['password'])]);
        $us = $pdo->fetch();
        if (empty($us))
            return false;

        return $us;
    }

    public function getUserInterest($id)
    {
        $pdo = $this->app->db->prepare("SELECT interests FROM users u WHERE u.id = ? ");
        $pdo->execute([$id]);

        return $pdo->fetch();
    }

    public function getUser($id)
    {
        $pdo = $this->app->db->prepare("SELECT * FROM users u WHERE u.id = ?");
        $pdo->execute([$id]);

        return $pdo->fetch();
    }

    public function getUsersByDate()
    {

        $date = new DateTime('-7 days');
        $date = $date->format('d/m/Y');

        $pdo = $this->app->db->prepare("SELECT SUBSTRING(created_at, 1, 10) as date, count(SUBSTRING(created_at, 1, 10)) as cpt FROM Users WHERE created_at >= ? group by date");
        $pdo->execute(array($date));
        return $pdo->fetchAll();
    }

    public function getAllUsers()
    {
        $user = $this->app->db->prepare("SELECT u.*, url, count(r.id_users_reported) as cptRe FROM Users u LEFT JOIN usersImage ui ON u.id=ui.id_users AND ui.isprofil = 1 LEFT JOIN reported r ON r.id_users_reported = u.id AND r.id is not NULL GROUP by r.id_users_reported, IF(r.id_users_reported IS NULL, u.id, 0) ");
        $user->execute();

        return $user->fetchAll();
    }

    public function getStringInterest($id)
    {
        $pdo = $this->app->db->prepare("SELECT ui.interest FROM Users u 
										INNER JOIN users_usersInterest uui ON uui.id_users = u.id
										INNER JOIN usersInterest ui ON uui.id_interest = ui.id
										WHERE u.id = ? ");
        $pdo->execute(array($id));
        foreach ($pdo->fetchAll() as $k => $v)
            $arr[] = $v['interest'];
        if (empty($arr))
            return false;

        return implode(',', $arr);
    }

    public function deleteInfo($id)
    {


        $pdo = $this->app->db->prepare("DELETE FROM usersImage WHERE id_users = ?");
        $pdo->execute(array($id));

        $pdo = $this->app->db->prepare("DELETE FROM users_usersInterest WHERE id_users = ?");
        $pdo->execute(array($id));

        $pdo = $this->app->db->prepare("DELETE FROM usersLocation WHERE id_users = ?");
        $pdo->execute(array($id));

        $pdo = $this->app->db->prepare("DELETE FROM notification WHERE id_users = ? OR id_users_send = ?");
        $pdo->execute(array($id, $id));

        $pdo = $this->app->db->prepare("DELETE FROM likable WHERE id_users = ? OR id_users_like = ?");
        $pdo->execute(array($id, $id));

        $pdo = $this->app->db->prepare("DELETE FROM chat WHERE id_auteur = ? OR id_receiver = ?");
        $pdo->execute(array($id, $id));

        $pdo = $this->app->db->prepare("DELETE FROM history WHERE id_users = ? OR id_users_visited = ?");
        $pdo->execute(array($id, $id));

        $pdo = $this->app->db->prepare("DELETE FROM usersblocked WHERE id_users = ? OR id_users_block = ?");
        $pdo->execute(array($id, $id));

        $pdo = $this->app->db->prepare("DELETE FROM reported WHERE id_users = ? OR id_users_reported = ?");
        $pdo->execute(array($id, $id));

        $pdo = $this->app->db->prepare("DELETE FROM Users WHERE id = ?");
        $pdo->execute(array($id));

    }
    public function countUserImage($id)
    {
        $pdo = $this->app->db->prepare("SELECT pics.url FROM users u 
										INNER JOIN pictures pics ON u.id = pics.id_user
										WHERE u.id = ? ");
        $pdo->execute(array($id));

        return count($pdo->fetchAll());
    }

    public function getImages($id)
    {
        $pdo = $this->app->db->prepare("SELECT pics.id, pics.url, pics.is_profil FROM users u 
										INNER JOIN pictures pics ON u.id = pics.id_user
										WHERE u.id = ? ");
        $pdo->execute(array($id));

        return $pdo->fetchAll();
    }

    public function isNotLoca($id)
    {
        $pdo = $this->app->db->prepare("SELECT * FROM usersLocation WHERE id_users = ?");
        $pdo->execute(array($id));

        return $pdo->fetch();
    }

    public function getStatuts($id, $idUser)
    {
        if ($id == $idUser)
            return -1;
        $like = $this->app->db->prepare("SELECT *
        FROM likable 
        WHERE (id_users = ? AND id_users_like = ?) OR (id_users = ? AND id_users_like = ?)");
        $like->execute(array($idUser, $id, $id, $idUser));
        if (count($like->fetchAll()) == 2)
        {
            return 2;
        }
        $like = $this->app->db->prepare("SELECT *
        FROM likable 
        WHERE id_users = ? AND id_users_like = ?");
        $like->execute(array($id, $idUser));
        if (count($like->fetchAll()) == 1)
        {
            return 1;
        }

        return 0;

    }

    public function getImageProfil($id)
    {
        $pdo = $this->app->db->prepare("SELECT pics.url FROM users u 
										INNER JOIN pictures pics ON u.id = pics.id_user
										WHERE u.id = ? AND pics.is_profil = 1");
        $pdo->execute(array($id));

        return $pdo->fetch();
    }

    public function getCity($id)
    {
        $pdo = $this->app->db->prepare("SELECT l.city FROM Users u
                                    INNER JOIN usersLocation l ON l.id_users = u.id
                                    WHERE u.id = ?");
        $pdo->execute(array($id));

        return $pdo->fetch();
    }
}