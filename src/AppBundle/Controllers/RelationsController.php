<?php
/**
 * Created by PhpStorm.
 * User: lekz
 * Date: 16/07/17
 * Time: 16:51
 */

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\Models\Likes;
use App\AppBundle\Models\Users;

class RelationsController extends Controller
{
    public function like($request, $response, $args)
    {
        $user = new Users($this->app);
        if (!$user->getImageProfil($this->getUserId()))
        {
            $response->withJson(['error' => "-1"]);
            return $response;
        }
        $likeId = $_POST['likeId'];
        $id = $this->getUserId();
        $response->withHeader('Content-type', 'application/json');

        $like = new Likes($this->app);
        $likable = $like->likeUser($id, $likeId);
        if ($likable == -1)
        {
            $like->deleteLike($id, $likeId);
        }
        else
        {
            $like->insert(['id_user' => $id,
                'id_user_like' => $likeId,
            ]);
        }

        return $response;
    }

}