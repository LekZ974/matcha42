<?php
/**
 * Created by PhpStorm.
 * User: lekz
 * Date: 16/07/17
 * Time: 16:51
 */

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
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
    }

}