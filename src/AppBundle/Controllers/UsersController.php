<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\Models\Interests;
use App\AppBundle\Models\Pictures;
use App\AppBundle\Models\Users;
use App\AppBundle\Upload;
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

            return $this->app->view->render($response, 'views/users/'.$args['profil'].'.html.twig', [
                'app' => new Controller($this->app),
                'userData' => $user->getUserData($id),
                'user' => $user->getUser($id) + ['profil' => $user->getImageProfil($id),
                        'hashtags' => unserialize($user->getUserInterest($id)['interests']),
                    ],
            ]);
//            return $this->app->view->render($response, 'views/users/'.$args['profil'].'.html.twig', [
//                'app' => new Controller($this->app),
//                'user' => $user->findById($id) + ['profil' => $user->getImageProfil($id),
//                    'hashtags' => unserialize($user->getUserInterest($id)['interests'])],
//                'userImages' => $user->getImages($id),
//            ]);
        }

        return $this->app->view->render($response, 'views/pages/homepage.html.twig', ['app' => new Controller($this->app)]);
    }

    public function viewProfil($request, $response, $args)
    {
        if ($this->isLogged()) {
            $user = new Users($this->app);
            $idProfil = $args['id'];
            $idUser = $this->getUserId();
            if ($idProfil === $idUser)
                $bool = 1;

            return $this->app->view->render($response, 'views/users/profil-page.html.twig', [
                'app' => new Controller($this->app),
                'owner' => $bool,
                'userData' => $user->getUserData($idProfil),
                'user' => $user->getUser($idProfil) + ['profil' => $user->getImageProfil($idProfil),
                        'hashtags' => unserialize($user->getUserInterest($idProfil)['interests']),
                    ],
            ]);
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
            $this->addInterest();
            $this->deleteInterest();
        }

        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('edit', ['profil' => $args['profil']]));
    }

    protected function deleteInterest()
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

    protected function addInterest()
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
                $interests = new Interests($this->app);
                if (count($userInterests) > 1)
                {
                    $userInterests = array_unique($userInterests);
                    foreach ($userInterests as $interest)
                    {
                        if ($interests->isSingle('interest', $interest))
                            $interests->insert(['interest' => $interest]);
                    }
                    $oldInterests = $user->getUserInterest($this->getUserId())['interests'];
                    if (!empty($oldInterests))
                    {
                        $oldInterests = unserialize($oldInterests);
                        foreach ($oldInterests as $interest)
                            array_push($userInterests, $interest);
                        $userInterests = array_unique($userInterests);
                    }
                }
                elseif (count($userInterests) === 1)
                {
                    if ($interests->isSingle('interest', $userInterests[0]))
                        $interests->insert(['interest' => $userInterests[0]]);
                    $oldInterests = $user->getUserInterest($this->getUserId())['interests'];
                    if (!empty($oldInterests))
                    {
                        $oldInterests = unserialize($oldInterests);
                        array_push($oldInterests, $userInterests[0]);
                        $userInterests = array_unique($oldInterests);
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

    protected function deleteItems($request)
    {
        $delete = $_POST['delete'];
        $multiDelete = $_POST['multiDelete'];
        if (isset($delete) && !empty($delete) || isset($multiDelete) && !empty($multiDelete))
        {
            $userImage = new Pictures($this->app);
            $items = [];
            foreach ($_POST as $elem)
            {
                $items[] = $userImage->findById($elem);
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
                foreach ($uploadFile->error as $error)
                    $this->app->flash->addMessage('error', $error);

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
            if (isset($ori))
            {
                switch ($ori){
                    case 'female':
                        $user->update($this->getUserId(), ['orientation' => 'female']);
                        break;
                    case 'male':
                        $user->update($this->getUserId(), ['orientation' => 'male']);
                        break;
                    default:
                        $user->update($this->getUserId(), ['orientation' => 'bisexuel']);
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