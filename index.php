<?php

require_once "vendor/autoload.php";

$friends_model = new AfekaFace\Models\Friends();
$user_model = new AfekaFace\Models\User();
$user_view = new AfekaFace\Views\User();
$users_controller = new AfekaFace\Controllers\Users();
$users_controller->setModel($user_model);
$users_controller->setFriendsModel($friends_model);
$users_controller->setView($user_view);

$router = new AfekaFace\Controllers\Router();
$router->setUsersController(
  $users_controller
);

$response = $router->run();

echo $response;
