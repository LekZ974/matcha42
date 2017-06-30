<?php
// Routes

$app->get('/', 'PagesController:indexAction')->setName('homepage');
$app->get('/signUp', 'SecurityController:signUpAction')->setName('signUp');
$app->post('/signUp-action', 'SecurityController:signUpForm')->setName('signUpForm');
$app->get('/logout', 'SecurityController:logout')->setName('logout');
$app->get('/home', 'UsersController:indexAction')->setName('home');
$app->post('/', 'SecurityController:signInForm')->setName('signInForm');

