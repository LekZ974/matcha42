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
use App\AppBundle\Security;
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
            if (null == $args)
            {
                $args['profil'] = 'basic';
            }
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
        $this->app->flash->addMessage('warning', 'Sign in or register you');

        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('signUp'));
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
                $profil = $this->addDistanceColumn(array_merge($user->getUserData($idProfil) , $user->getImageProfil($idProfil)));
            else
                $profil = $user->getUserData($idProfil);
            if ($co->isInactive($idProfil))
                $co->isDisconnected($idProfil);
            $isLike = $like->isLike($idUser, $idProfil);
            $this->upPopularity($idProfil, 1);

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
            $this->addPhoto($request, $response, $args);
            $this->updateBasic($request, $response, $args);
            $this->deleteItems($request, $response, $args);
            $this->updateAvatar($request, $response, $args);
            $this->addInterest($request, $response, $args);
            $this->deleteInterest($request, $response, $args);
            $this->updateLocation($request, $response, $args);
        }

        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('edit', ['profil' => $args['profil']]));
    }

    public function updateLocation($request, $response, $args)
    {
        $userLocation = new UserLocation($this->app);
        $oldUserLocation = $userLocation->findOne('id_user', $this->getUserId());
        if (isset($_POST['country'], $_POST['region'], $_POST['city'], $_POST['zipCode']))
        {
            $country = Security::secureDB($_POST['country']);
            $region = Security::secureDB($_POST['region']);
            $city = Security::secureDB($_POST['city']);
            $zipCode = Security::secureDB($_POST['zipCode']);
            $location = ['country' => $country, 'region' => $region, 'city' => $city, 'zipCode' => $zipCode, 'lat' => Security::secureDB($_POST['lat']), 'lon' => Security::secureDB($_POST['lon']), 'id_user' => $this->getUserId()];
            $location = array_filter($location);
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
        if (empty($location['lat']) || empty($location['lon']))
        {
            $location['lat'] = $oldUserLocation['lat'];
            $location['lon'] = $oldUserLocation['lon'];
        }
        if (empty($oldUserLocation))
            $userLocation->insert($location);
        elseif (array_intersect($oldUserLocation, $location) != $location)
            $userLocation->updateLink('id_user', $this->getUserId(), $location);

        return true;
    }

    public function getUserInfo($request, $response, $args)
    {
        $user = new Users($this->app);
        $user = $user->getUserData($this->getUserId());
        $response = $response->withHeader('Content-type', 'application/json');
        $response = $response->withJson(['user' => $user]);

        return $response;
    }

    protected function deleteInterest($request, $response, $args)
    {
        if (isset($_POST['deleteInterest']))
            $userInterest = Security::secureDB($_POST['deleteInterest']);
        if (isset($userInterest) && !empty($userInterest))
        {
            $user = new Users($this->app);
            $interests = new UserInterests($this->app);
            $userInterests = unserialize($user->getUserInterest($this->getUserId())['interests']);
            $res = array_search($userInterest, $userInterests);
            array_splice($userInterests, $res, 1);
            $interestExist = $interests->findBy2Column('interest', $userInterest, 'id_user', $this->getUserId());
            foreach ($interestExist as $interest)
                $interests->delete($interest['id']);
            $user->update($this->getUserId(), ['interests' => serialize($userInterests)]);
        }
    }

    protected function addInterest($request, $response, $args)
    {
        if (isset($_POST['interests']))
            $userInterests = Security::secureDB($_POST['interests']);
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
                    $oldInterests = $user->getUserInterest($this->getUserId())['interests'];
                    if (!empty($oldInterests))
                    {
                        $oldInterests = unserialize($oldInterests);
                        foreach ($userInterests as $interest)
                        {
                            $interestExist = $interests->findBy2Column('interest', $interest, 'id_user', $this->getUserId());
                            if ($interestExist == null)
                            {
                                if (count($oldInterests) + count($interest) > 20)
                                    $this->app->flash->addMessage('error', 'You have reached the maximum amount of interest allowed (limited to 20)');
                                else
                                {
                                    array_push($oldInterests, $interest);
                                    $interests->insert([
                                        'interest' => $interest,
                                        'id_user' => $this->getUserId(),
                                    ]);
                                };
                            }
                        }
                        $newUserInterests = array_unique($oldInterests);
                    }
                    else
                    {
                        foreach ($userInterests as $interest)
                        {
                            $interestExist = $interests->findBy2Column('interest', $interest, 'id_user', $this->getUserId());
                            if ($interestExist == null)
                            {
                                if (count($userInterests) > 20)
                                {
                                    $this->app->flash->addMessage('error', 'You have reached the maximum amount of interest allowed (limited to 20)');
                                }
                                else
                                {
                                    $interests->insert([
                                        'interest' => $interest,
                                        'id_user' => $this->getUserId(),
                                    ]);
                                };
                            }
                        }
                        $newUserInterests = array_unique($userInterests);
                    }
                }
                elseif (count($userInterests) === 1)
                {
                    $interest = $userInterests[0];
                    $oldInterests = $user->getUserInterest($this->getUserId())['interests'];
                    if (!empty($oldInterests))
                    {
                        $oldInterests = unserialize($oldInterests);
                        array_push($oldInterests, $interest);
                        $newUserInterests = array_unique($oldInterests);
                        if (count($newUserInterests) > 20)
                        {
                            $this->app->flash->addMessage('error', 'You have reached the maximum amount of interest allowed (limited to 20)');

                            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('edit', ['profil' => $args['profil']]));
                        }
                        else
                        {
                            $interestExist = $interests->findBy2Column('interest', $interest, 'id_user', $this->getUserId());
                            if ($interestExist == null)
                            {
                                $interests->insert([
                                    'interest' => $interest,
                                    'id_user' => $this->getUserId(),
                                ]);;
                            }
                        }
                    }
                }
                $user->update($this->getUserId(), ['interests' => serialize($newUserInterests)]);
            }
            else
                $this->app->flash->addMessage('error', 'You use an invalid character');
        }
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('edit', ['profil' => $args['profil']]));
    }

    protected function updateAvatar($request, $response, $args)
    {
        if (isset($_POST['getAvatar']))
            $avatar = Security::secureDB($_POST['getAvatar']);
        if (!empty($avatar) || !empty($avatar))
        {
            $userImage = new Pictures($this->app);
            $userImage->setImageAsProfil($avatar, $this->getUserId());
            $this->app->flash->addMessage('success', 'Avatar is updated');
        }
    }

    public function deleteItems($request, $response, $args)
    {
        if (isset($_POST['delete']))
            $delete = Security::secureDB($_POST['delete']);
        if (isset($_POST['multiDelete']))
            $multiDelete = Security::secureDB($_POST['multiDelete']);
        if (isset($_POST['type']))
            $type = Security::secureDB($_POST['type']);
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

    protected function addPhoto($request, $response, $args)
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

    protected function updateBasic($request, $response, $args)
    {
        if (isset($_POST['orientationF'], $_POST['orientationM']))
            $ori = Security::secureDB($_POST['orientationF']).Security::secureDB($_POST['orientationM']);
        if (isset($_POST['gender']))
            $gender = Security::secureDB($_POST['gender']);
        $resume = Security::secureDB($_POST['resume']);
        if (!empty($ori) || !empty($gender) || !empty($resume))
        {
            $user = new Users($this->app);
            $user->findById($this->getUserId());
            if (isset($gender) && !empty($gender))
            {
                $user->update($this->getUserId(), ['gender' => $gender]);
            }
            if (!empty($ori))
            {
                switch ($ori){
                    case 'female':
                        $user->update($this->getUserId(), ['orientation' => 'woman']);
                        break;
                    case 'male':
                        $user->update($this->getUserId(), ['orientation' => 'man']);
                        break;
                    default:
                        $user->update($this->getUserId(), ['orientation' => 'bisexual']);
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