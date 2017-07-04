<?php
// Routes

$app->get('/', 'PagesController:indexAction')->setName('homepage');
$app->post('/', 'SecurityController:signInForm')->setName('signInForm');
$app->get('/signUp', 'SecurityController:signUpAction')->setName('signUp');
$app->post('/signUp', 'SecurityController:signUpForm')->setName('signUpForm');
$app->get('/logout', 'SecurityController:logout')->setName('logout');
$app->get('/home', 'UsersController:indexAction')->setName('home');
$app->get('/home/{profil}', 'UsersController:indexAction')->setName('edit');

