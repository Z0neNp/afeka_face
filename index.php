<?php

require_once "vendor/autoload.php";

$encryptor = new AfekaFace\Encryptor();

$friends_model = new AfekaFace\Models\Friends();
$user_model = new AfekaFace\Models\User();

$authentication_view = new AfekaFace\Views\Authentication();
$home_view = new AfekaFace\Views\Home();
$user_view = new AfekaFace\Views\User();

$home_controller = new AfekaFace\Controllers\Home();
$users_controller = new AfekaFace\Controllers\Users();

$home_controller->setView($home_view);

$users_controller->setAuthenticationView($authentication_view);
$users_controller->setEncryptor($encryptor);
$users_controller->setFriendsModel($friends_model);
$users_controller->setModel($user_model);
$users_controller->setView($user_view);

$router = new AfekaFace\Controllers\Router();
$router->setUsersController($users_controller);
$router->setHomeController($home_controller);

$response = $router->run();

echo $response;
