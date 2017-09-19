<?php

// Get container
$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
    $settings = $container->get('settings')['renderer'];
    $view = new \Slim\Views\Twig($settings['template_path'], [
        'cache' => false
//        'cache' => $settings['template_cache']
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    $view->getEnvironment()->addGlobal('flash', $container['flash']);

    return $view;
};

// UsersController
$container['UsersController'] = function ($container)
{
    return new App\AppBundle\Controllers\UsersController($container);
};

// PagesController
$container['PagesController'] = function ($container)
{
    return new App\AppBundle\Controllers\PagesController($container);
};

// SecurityController
$container['SecurityController'] = function ($container)
{
    return new App\AppBundle\Controllers\SecurityController($container);
};

// RelationsController
$container['RelationsController'] = function ($container)
{
    return new App\AppBundle\Controllers\RelationsController($container);
};

// ChatController
$container['ChatController'] = function ($container)
{
    return new App\AppBundle\Controllers\ChatController($container);
};

// Db connect
$container['db'] = function($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=".$db['host'].";dbname=".$db['dbname'],
        $db['user'], $db['pass'], array(\PDO::MYSQL_ATTR_INIT_COMMAND =>  'SET NAMES utf8'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");

    return $pdo;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Validator FORM
$container['formValidator'] = function($c)
{
    return new \App\AppBundle\FormValidator($_POST, $c);
};

$container['mail'] = function($c)
{
    $set = $c['settings']['mail'];
    return new \App\AppBundle\Mail($c, $set);
};

// Flash Messages
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};
