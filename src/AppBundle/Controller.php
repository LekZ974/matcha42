<?php

namespace App\AppBundle;
use App\AppBundle\Models\Likes;
use App\AppBundle\Models\Pictures;
use App\AppBundle\Models\Notifications;
use App\AppBundle\Models\Users;
use DateTime;
use App\AppBundle\Models\UserLocation;
use phpDocumentor\Reflection\Location;

/**
 * @author Alexandre Hoareau <ahoareau@student.42.fr>
 */
class Controller
{
    protected $app;
    protected $view;
    protected $router;
    protected $flash;

    public function __construct($container)
    {
        date_default_timezone_set('Europe/Paris');
        $this->app = $container;
        $this->view = $container->view;
        $this->flash = $container->flash;
        $this->router = $container->router;
    }

    public function isLogged()
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user']))
        {
            $co = new IsConnected($this->app);
            if ($co->isInactive($_SESSION['user']['id']))
            {
                $co->isDisconnected($_SESSION['user']['id']);
                return false;
            }
            $co->connect($_SESSION['user']['id']);
            return true;
        }
        return false;
    }

    public function getUserId()
    {
        if ($this->isLogged())
        {
            return ($_SESSION['user']['id']);
        }
        return false;
    }

    public function getNotifications()
    {
        $users = new Notifications($this->app);
        $notifications = $users->getNotification($this->getUserId());

        return $notifications;
    }

    public function getUnreadNotifications()
    {
        $users = new Notifications($this->app);
        $notifications = $users->getUnreadNotification($this->getUserId());

        return $notifications;
    }

    public function getLastNotifications()
    {
        $users = new Notifications($this->app);
        $notifications = $users->getLastNotification($this->getUserId(), 10);
        $date = new DateTime();
        $lastNotification = null;
        $i = 0;
        foreach ($notifications as $notification)
        {
            print_r(' ');
            if (abs(time() - strtotime($notification['dateNotif'])) < 10)
            {
                $lastNotification[$i] = $notification;
                $i++;
            }
        }

        return $lastNotification;
    }

    public function getCountUnreadNotif()
    {
        $notif = new Notifications($this->app);
        $nb = $notif->getCountUnreadNotif($this->getUserId());

        return $nb;
    }

    public function hasProfilPic()
    {
        $img = new Pictures($this->app);
        $img = $img->getProfilPic($this->getUserId());
        if ($img != null)
            return $img;
        return false;
    }

    public function getIp()
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ] as $key ) {
            if ( array_key_exists( $key, $_SERVER ) === true ) {
                // … et pour chacune de leurs valeurs…
                foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
                    $ip = trim( $ip );
// if is an IP address but not an intern (192.0.0.1) or a loopback (127.0.0.1)
                    if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false
                        && ( ( ip2long( $ip ) & 0xff000000 ) != 0x7f000000 ) ) {
                        return $ip;
                    }
//for testing on localhost
                    else
                        return '90.91.123.174';
                }
            }
        }
        return false;
    }

    public function removeAccents($str, $charset)
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);

        return $str;
    }

    public function subTextIfTooLong($text, $max_lenght, $replace)
    {
        if (strlen($text) >= $max_lenght) {
            $text = substr($text, 0, $max_lenght);
            $lastWord = strrpos($text, ' ');
            $text = substr($text, 0, $lastWord);

            return $text . ' ' . $replace;
        }
        return $text;
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function addDistanceColumn($data)
    {
        $users = new Users($this->app);
        $user = $users->getUserData($this->getUserId());
        $data = $data + ['distance' => round($this->distance($user['lat'], $user['lon'], $data['lat'], $data['lon'], 'K'), 2)];
        uasort($suggests, function ($a, $b){
            return $a['distance'] - $b['distance'];
        });
        return $data;
    }
}