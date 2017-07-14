<?php

namespace App\AppBundle;
use App\AppBundle\Models\Pictures;
use App\AppBundle\Models\UserLocation;

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
            $co->connect();
            $location = $this->getLocation();
            $userLocation = new UserLocation($this->app);
            $oldUserLocation = $userLocation->findOne('id_user', $this->getUserId());
            if (empty($oldUserLocation))
                $userLocation->insert($location);
            elseif (array_intersect($oldUserLocation, $location) != $location)
            {
                $userLocation->updateLink('id_user', $this->getUserId(), $location);
            }
            return true;
        }
        return false;
    }

    public function getUserId()
    {
//        if ($this->isLogged())
//        {
            return ($_SESSION['user']['id']);
//        }
//        return false;
    }

    public function getProfilPic()
    {
        $img = new Pictures($this->app);
        $img = $img->getProfilPic($this->getUserId());
        return $img;
    }

    public function getIp()
    {
        // IP si internet partagé
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        // IP derrière un proxy
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        // Sinon : IP normale
        else {
            return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        }
    }

    public function getLocation()
    {
        $gi = geoip_open(realpath("/home/lekz/Téléchargements/GeoLiteCity.dat"),GEOIP_STANDARD);

        $record = geoip_record_by_addr($gi, '90.91.123.174');

        if (isset($record) && !empty($record))
        {
            $country = $record->country_name;
            $la = $record->latitude;
            $lo = $record->longitude;
            $city = $record->city;
            if (empty($city))
            {
                $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$la.",".$lo."&key=AIzaSyA_ZXYFc53naqHWpByr96LcH9yZUDF0YFY";
                if($content = file_get_contents($url))
                    $city = json_decode($content)->results[0]->address_components[2]->long_name;
            }
            geoip_close($gi);

            return ['country' => $country, 'region' => $city, 'city' => $city, 'lat' => $la, 'lon' => $lo, 'id_user' => $this->getUserId()];
        }
        geoip_close($gi);

        return ['error' => 'An error is occurred'];
    }
}