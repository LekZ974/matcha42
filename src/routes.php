<?php
// Routes

$app->get('/', 'PagesController:indexAction')->setName('homepage');
$app->post('/', 'SecurityController:signInForm')->setName('signInForm');

$app->get('/signUp', 'SecurityController:signUpAction')->setName('signUp');
$app->post('/signUp', 'SecurityController:signUpForm')->setName('signUpForm');

$app->get('/activate', 'SecurityController:activateAccountAction')->setName('activate');
$app->get('/forgotPassword', 'SecurityController:forgotPasswordAction')->setName('forgotPassword');
$app->post('/forgotPassword', 'SecurityController:forgotPassword')->setName('forgotPassword');
$app->get('/resetPassword', 'SecurityController:resetPasswordAction')->setName('resetPassword');
$app->post('/resetPassword', 'SecurityController:resetPassword')->setName('resetPassword');

$app->get('/logout', 'SecurityController:logout')->setName('logout');

$app->get('/home', 'UsersController:indexAction')->setName('home');

$app->get('/home/{profil}', 'UsersController:indexAction')->setName('edit');
$app->post('/home/{profil}', 'UsersController:editUser')->setName('edit');

$app->get('/users/view/{id}', 'UsersController:viewProfil')->setName('viewProfil');

$app->get('/chat/{id}', 'ChatController:indexAction')->setName('chatPage');
$app->post('/chat/{id}', 'ChatController:sendMessage')->setName('sendMessage');
$app->get('/chat', 'ChatController:getListAction')->setName('chat');

$app->get('/map', 'PagesController:mapLocation')->setName('mapLocation');
$app->get('/search', 'PagesController:mapLocation')->setName('mapLocation');


//AJAX

$app->post('/updateLocation', 'UsersController:updateLocation')->setName('updateLocation');
$app->post('/like', 'RelationsController:like')->setName('like');
$app->get('/lastNotif', 'RelationsController:lastNotif')->setName('lastNotif');
$app->get('/notif', 'RelationsController:notif')->setName('notif');
$app->get('/unreadNotif', 'RelationsController:unreadNotif')->setName('unreadNotif');
$app->get('/allNotif', 'RelationsController:allNotif')->setName('allNotif');
$app->get('/countNotif', 'RelationsController:countNotif')->setName('countNotif');
$app->post('/readNotif', 'RelationsController:readNotif')->setName('unreadNotif');
$app->get('/getMessages/{id}', 'ChatController:getMessagesAction')->setName('getMessages');
$app->post('/delete', 'UsersController:deleteItems')->setName('delete');
$app->post('/report', 'RelationsController:reportAsFake')->setName('reportAsFake');
$app->post('/block', 'RelationsController:blockUser')->setName('block');