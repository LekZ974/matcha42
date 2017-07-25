<?php
// Routes

$app->get('/', 'PagesController:indexAction')->setName('homepage');
$app->post('/', 'SecurityController:signInForm')->setName('signInForm');

$app->get('/signUp', 'SecurityController:signUpAction')->setName('signUp');
$app->post('/signUp', 'SecurityController:signUpForm')->setName('signUpForm');

$app->get('/activate', 'SecurityController:activateAccountAction')->setName('activate');

$app->get('/logout', 'SecurityController:logout')->setName('logout');

$app->get('/home', 'UsersController:indexAction')->setName('home');

$app->get('/home/{profil}', 'UsersController:indexAction')->setName('edit');
$app->post('/home/{profil}', 'UsersController:editUser')->setName('edit');

$app->get('/users/view/{id}', 'UsersController:viewProfil')->setName('viewProfil');

//AJAX

$app->post('/updateLocation', 'UsersController:updateLocation')->setName('updateLocation');
$app->post('/like', 'RelationsController:like')->setName('like');
$app->get('/notif', 'RelationsController:unreadNotif')->setName('notif');
$app->get('/countNotif', 'RelationsController:countNotif')->setName('countNotif');
$app->post('/readNotif', 'RelationsController:readNotif')->setName('unreadNotif');