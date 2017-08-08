<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\Models\IpLocation;
use App\AppBundle\Models\Likes;
use App\AppBundle\Models\Messages;
use App\AppBundle\Models\Notifications;
use App\AppBundle\Models\UserInterests;
use App\AppBundle\Models\Pictures;
use App\AppBundle\Models\UserLocation;
use App\AppBundle\Models\Users;
use App\AppBundle\Upload;
use App\AppBundle\IsConnected;
use Prophecy\Exception\Exception;
use Psr\Http\Message\ResponseInterface;

class UsersController extends Controller
{
    public function indexAction($request, $response, $args)
    {
        if ($this->isLogged())
        {
            $user = new Users($this->app);
            $id = $this->getUserId();
            $images = new Pictures($this->app);
            if ($user->getImageProfil($id))
                $profil = array_merge($user->getUserData($id) , $user->getImageProfil($id));
            else
                $profil = $user->getUserData($id);

            return $this->app->view->render($response, 'views/users/'.$args['profil'].'.html.twig', [
                'app' => new Controller($this->app),
                'user' => $profil,
                'hashtags' => unserialize($user->getUserInterest($id)['interests']),
                'images' => $images->getImagesByIdUser($id),
            ]);
        }

        return $this->app->view->render($response, 'views/pages/homepage.html.twig', ['app' => new Controller($this->app)]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return bool
     */
    public function viewProfil($request, $response, $args)
    {
        if ($this->isLogged()) {
            $user = new Users($this->app);
            $co = new IsConnected($this->app);
            $images = new Pictures($this->app);
            $like = new Likes($this->app);
            $idProfil = $args['id'];
            $idUser = $this->getUserId();
            $bool = 0;
            if ($idProfil === $idUser)
                $bool = 1;
            if ($user->getImageProfil($idProfil))
                $profil = array_merge($user->getUserData($idProfil) , $user->getImageProfil($idProfil));
            else
                $profil = $user->getUserData($idProfil);
            if ($co->isInactive($idProfil))
                $co->isDisconnected($idProfil);
            $isLike = $like->isLike($idUser, $idProfil);


            return $this->app->view->render($response, 'views/users/profil-page.html.twig', [
                'app' => new Controller($this->app),
                'owner' => $bool,
                'user' => $profil + ['isLike' => $isLike],
                'hashtags' => unserialize($user->getUserInterest($idProfil)['interests']),
                'images' => $images->getImagesByIdUser($idProfil),
                'match' => $like->isMatch($idUser, $idProfil),
            ]);
        }
        else
        {
            $this->app->flash->addMessage('warning', 'Sign in or register you');
            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('signUp'));
        }
    }

    public function editUser($request, $response, $args)
    {
        if ($this->isLogged())
        {
            $this->addPhoto($request);
            $this->updateBasic($request);
            $this->deleteItems($request);
            $this->updateAvatar($request);
            $this->addInterest($request, $response, $args);
            $this->deleteInterest($request, $response, $args);
            $this->updateLocation($request, $response, $args);
        }

        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('edit', ['profil' => $args['profil']]));
    }

    public function updateLocation($request, $response, $args)
    {
        $country = $_POST['country'];
        $region = $_POST['region'];
        $city = $_POST['city'];
        $zipCode = $_POST['zipCode'];
        if (isset($country, $region, $city, $zipCode) && !empty($country) && !empty($region) && !empty($city) && !empty($zipCode))
        {
            $location = ['country' => $country, 'region' => $region, 'city' => $city, 'zipCode' => $zipCode, 'lat' => $_POST['lat'], 'lon' => $_POST['lon'], 'id_user' => $this->getUserId()];
            $location = array_map(function($elem){
                $elem = $this->removeAccents($elem, 'utf-8');

                return $elem;
            }, $location);
        }
        else {
            $ip = $this->getIp();
            if ($ip) {
                $gi = geoip_open(realpath(__DIR__ . "/../../../app/Geoloc/GeoLiteCity.dat"),GEOIP_STANDARD);

                $record = geoip_record_by_addr($gi,$ip);

                $la = $record->latitude;
                $lo = $record->longitude;

                $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $la . "," . $lo . "&key=AIzaSyA_ZXYFc53naqHWpByr96LcH9yZUDF0YFY";

                if($json = file_get_contents($url))
                {
                    $informations = json_decode($json, true);
                    if(!$informations)
                    {
                        die("Erreur");
                    }
                    else
                    {
                        $location = [
                            'country' => $informations['results'][0]['address_components'][5]['long_name'],
                            'region' => $informations['results'][0]['address_components'][4]['long_name'],
                            'zipCode' => $informations['results'][0]['address_components'][6]['long_name'],
                            'city' => $informations['results'][0]['address_components'][2]['long_name'],
                            'lat' => $la,
                            'lon' => $lo,
                            'id_user' => $this->getUserId(),
                        ];
                    }
                }
                else
                {
                    echo "Erreur";
                }
                geoip_close($gi);

            } else {
                $this->app->flash->addMessage('warning', 'You can\'t see people around you, active location');

                return false;
            }
        }
        $userLocation = new UserLocation($this->app);
        $oldUserLocation = $userLocation->findOne('id_user', $this->getUserId());
        if (empty($oldUserLocation))
            $userLocation->insert($location);
        elseif (array_intersect($oldUserLocation, $location) != $location)
            $userLocation->updateLink('id_user', $this->getUserId(), $location);

        return true;
    }

    protected function deleteInterest($request, $response, $args)
    {
        $userInterest = $_POST['deleteInterest'];
        if (isset($userInterest) && !empty($userInterest))
        {
            $user = new Users($this->app);
            $userInterests = unserialize($user->getUserInterest($this->getUserId())['interests']);
            $res = array_search($userInterest, $userInterests);
            array_splice($userInterests, $res, 1);
            $user->update($this->getUserId(), ['interests' => serialize($userInterests)]);
        }
    }

    protected function addInterest($request, $response, $args)
    {
        $userInterests = $_POST['interests'];
        if (isset($userInterests) && !empty($userInterests))
        {
            $_POST['deleteInterest'] = null;
            mb_internal_encoding('UTF-8');
            $userInterests = mb_strtolower($userInterests);
            $userInterests = trim(preg_replace('/[^a-zA-Z0-9áàâäãåçéèêëíìîïñóòôöõøúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+/', ' ', $userInterests));
            $user = new Users($this->app);
            if (preg_match("/[a-zA-Z0-9áàâäãåçéèêëíìîïñóòôöõøúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]/", $userInterests))
            {
                $userInterests = preg_split("/[^a-zA-Z0-9áàâäãåçéèêëíìîïñóòôöõøúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+/", $userInterests);
                array_walk($userInterests, function(&$interest){
                    $interest = '#'.$interest;

                    return $interest;
                });
                $interests = new UserInterests($this->app);
                if (count($userInterests) > 1)
                {
                    $userInterests = array_unique($userInterests);
                    foreach ($userInterests as $interest)
                    {
                        if ($interests->isSingle('interest', $interest))
                        {
                            $interests->insert([
                                'interest' => $interest,
                                'id_user' => $this->getUserId(),
                            ]);
                        }
                    }
                    $oldInterests = $user->getUserInterest($this->getUserId())['interests'];
                    if (!empty($oldInterests))
                    {
                        $oldInterests = unserialize($oldInterests);
                        foreach ($oldInterests as $interest)
                            array_push($userInterests, $interest);
                        $userInterests = array_unique($userInterests);
                        if (count($userInterests) > 20)
                        {
                            $this->app->flash->addMessage('error', 'You have reached the maximum amount of interest allowed (limited to 20)');

                            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('edit', ['profil' => $args['profil']]));
                        }
                    }
                }
                elseif (count($userInterests) === 1)
                {
                    if ($interests->isSingle('interest', $userInterests[0]))
                    {
                        $interests->insert([
                            'interest' => $userInterests[0],
                            'id_user' => $this->getUserId(),
                        ]);;
                    }
                    $oldInterests = $user->getUserInterest($this->getUserId())['interests'];
                    if (!empty($oldInterests))
                    {
                        $oldInterests = unserialize($oldInterests);
                        array_push($oldInterests, $userInterests[0]);
                        $userInterests = array_unique($oldInterests);
                        if (count($userInterests) > 20)
                        {
                            $this->app->flash->addMessage('error', 'You have reached the maximum amount of interest allowed (limited to 20)');

                            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('edit', ['profil' => $args['profil']]));
                        }
                    }
                }
                $user->update($this->getUserId(), ['interests' => serialize($userInterests)]);
            }
            else
                $this->app->flash->addMessage('error', 'You use an invalid character');
        }
    }

    protected function updateAvatar($request)
    {
        $avatar = $_POST['getAvatar'];
        if (!empty($avatar) || !empty($avatar))
        {
            $userImage = new Pictures($this->app);
            $userImage->setImageAsProfil($avatar, $this->getUserId());
            $this->app->flash->addMessage('success', 'Avatar is updated');
        }
    }

    public function deleteItems($request, $response)
    {
        $delete = $_POST['delete'];
        $multiDelete = $_POST['multiDelete'];
        $type = $_POST['type'];
        if (isset($delete) && !empty($delete) && !empty($type) || isset($multiDelete) && !empty($multiDelete) && !empty($type))
        {
            $userNotif = new Notifications($this->app);
            $userMessage = new Messages($this->app);
            if ($type === 'notif')
            {
                $items = [];
                foreach ($_POST as $elem)
                {
                    $items[] = $userNotif->findById($elem);
                }
                if (!empty($items[0]))
                {
                    foreach ($items as $item)
                    {
                        $userNotif->deleteSpecial('id', $item['id']);
                        if ($userMessage->find('idNotif', $item['id']) && $this->getUserId() == $item['id_user'])
                            $userMessage->deleteSpecial('idNotif', $item['id']);
                    }
                }
            }
            elseif ($type === 'message')
            {
                $items = [];
                foreach ($_POST as $elem)
                {
                    $items[] = $userMessage->findById($elem);
                }
                if (!empty($items[0]))
                {
                    foreach ($items as $item)
                    {
                        if ($this->getUserId() == $item['id_user'])
                        {
                            $userMessage->deleteSpecial('id', $item['id']);
                            $userNotif->deleteSpecial('id', $item['idNotif']);
                        }
                    }
                }
            }
            elseif ($type === 'pics')
            {
                $userImage = new Pictures($this->app);
                $items = [];
                foreach ($_POST as $elem)
                {
                    if (is_numeric($elem)){
                        $items[] = $userImage->findById($elem);
                    }
                }
                if (!empty($items[0]))
                {
                    foreach ($items as $item)
                    {
                        if ($userImage->deleteImage($item['id'], $this->getUserId()))
                        {
                            unlink(__DIR__.'/../../../public'.$item['url']);
                            $this->app->flash->addMessage('success', 'Picture is deleted');
                        }
                        else
                            $this->app->flash->addMessage('error', 'An error is occurred');
                    }
                }
                else
                    $this->app->flash->addMessage('warning', 'Nothing to delete');
            }
        }

        return $response;
    }

    protected function addPhoto($request)
    {
        if (isset($_FILES['photoUser']) && !empty($_FILES['photoUser']) || isset($_FILES['avatarUser']) && !empty($_FILES['avatarUser']))
        {
            $userImage = new Pictures($this->app);
            $user = new Users($this->app);
            $uploadFile = new Upload($request->getUploadedFiles());
            if ($user->countUserImage($this->getUserId()) < 5)
            {
                $file = $uploadFile->uploadIsValid(__DIR__.'/../../../public/image/', 5000000);
                if (!empty($uploadFile->error))
                {
                    foreach ($uploadFile->error as $error)
                        $this->app->flash->addMessage('error', $error);

                    return false;
                }

//UPLOAD photo profil
//                if (isset($_FILES['avatarUser']) && !empty($_FILES['avatarUser']))
//                {
//                    print_r('toto');
//                    if ($userImage->insert([
//                        'id_user' =>  $this->getUserId(),
//                        'url' => '/image/'.$file,
//                        'is_profil' => 1,
//                        'created_at' => date("d/m/Y H:i:s"),
//                    ]))
//                        $this->app->flash->addMessage('success', 'Your avatar is updated');
//                    else
//                        $this->app->flash->addMessage('error', 'an error is occurred');
//                }
//                else
//                {
                    if (!$user->getImageProfil($this->getUserId()))
                        $bool = 1;
                    else
                        $bool = 0;
                    if ($userImage->insert([
                        'id_user' =>  $this->getUserId(),
                        'url' => '/image/'.$file,
                        'is_profil' => $bool,
                        'created_at' => date("d/m/Y H:i:s"),
                    ]))
                        $this->app->flash->addMessage('success', 'Picture is add');
                    else
                        $this->app->flash->addMessage('error', 'an error is occurred');
//                }
            }
            else
                $this->app->flash->addMessage('error', 'Sorry, you have reached the maximum number of pictures');
        }
    }

    protected function updateBasic($request)
    {
        $ori = $_POST['orientationF'].$_POST['orientationM'];
        $gender = $_POST['gender'];
        $resume = $_POST['resume'];
        if (!empty($ori) || !empty($gender) || !empty($resume))
        {
            $user = new Users($this->app);
            $user->findById($this->getUserId());
            if (isset($gender))
            {
                $user->update($this->getUserId(), ['gender' => $gender]);
            }
            if (!empty($ori))
            {
                switch ($ori){
                    case 'female':
                        $user->update($this->getUserId(), ['orientation' => 'Woman']);
                        break;
                    case 'male':
                        $user->update($this->getUserId(), ['orientation' => 'Man']);
                        break;
                    default:
                        $user->update($this->getUserId(), ['orientation' => 'Bisexuel']);
                        break;
                }
            }
            if (!empty($resume))
            {
                $user->update($this->getUserId(), ['resume' => $resume]);
            }
            $this->app->flash->addMessage('success', 'Account is updated');
        }
    }
}