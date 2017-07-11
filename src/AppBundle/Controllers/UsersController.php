<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
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
                'user' => $user->findById($id) + ['profil' => $user->getImageProfil($id)],
                'userImages' => $user->getImages($id),
            ]);
        }

        return $this->app->view->render($response, 'views/pages/homepage.html.twig', ['app' => new Controller($this->app)]);
    }

    public function editUser($request, $response, $args)
    {
        if ($this->isLogged())
        {
            $this->addPhoto($request);
            $this->updateBasic($request);
        }

        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('edit', ['profil' => $args['profil']]));
    }

    protected function addPhoto($request)
    {
        if (isset($_FILES['photoUser']) && !empty($_FILES['photoUser']))
        {
            $userImage = new Pictures($this->app);
            $user = new Users($this->app);
            $uploadFile = new Upload($request->getUploadedFiles());
            if ($user->countUserImage($this->getUserId()) < 5)
            {
                $file = $uploadFile->uploadIsValid(__DIR__.'/../../../public/image/', 5000000);
                foreach ($uploadFile->error as $error)
                    $this->app->flash->addMessage('error', $error);

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
            }
            else
                $this->app->flash->addMessage('error', 'Sorry, you have reached the maximum number of pictures');
        }
    }

    protected function updateBasic($request)
    {
        if (isset($_POST))
        {
            $oriM = $_POST['orientationM'];
            $oriF = $_POST['orientationF'];
            $gender = $_POST['gender'];
            $user = new Users($this->app);
            $user->findById($this->getUserId());
            if (isset($gender))
            {
                $user->update($this->getUserId(), ['gender' => $gender]);
            }
            if (isset($oriM) || isset($oriF))
            {
                $ori = $oriM.$oriF;
                switch ($ori){
                    case $oriF:
                        $user->update($this->getUserId(), ['orientation' => $oriF]);
                        break;
                    case $oriM:
                        $user->update($this->getUserId(), ['orientation' => $oriM]);
                        break;
                    default:
                        $user->update($this->getUserId(), ['orientation' => 'bisexuel']);
                        break;
                }
            }
            $this->app->flash->addMessage('success', 'Account is updated');
        }
        else{
            $this->app->flash->addMessage('warning', 'Nothing to change');
        }
    }
}