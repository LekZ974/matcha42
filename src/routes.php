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

$app->get('/chat/{id}', 'ChatController:indexAction')->setName('chatPage');
$app->post('/chat/{id}', 'ChatController:indexForm')->setName('chatMessage');
$app->get('/listChat', 'ChatController:listAction')->setName('chat');

//AJAX

$app->post('/updateLocation', 'UsersController:updateLocation')->setName('updateLocation');
$app->post('/like', 'RelationsController:like')->setName('like');
$app->get('/lastNotif', 'RelationsController:lastNotif')->setName('lastNotif');
$app->get('/notif', 'RelationsController:notif')->setName('notif');
$app->get('/unreadNotif', 'RelationsController:unreadNotif')->setName('unreadNotif');
$app->get('/allNotif', 'RelationsController:allNotif')->setName('allNotif');
$app->get('/countNotif', 'RelationsController:countNotif')->setName('countNotif');
$app->post('/readNotif', 'RelationsController:readNotif')->setName('unreadNotif');