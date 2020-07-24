<?php

require_once "src/Database.php";
require_once "src/Encryptor.php";
require_once "src/Controllers/Users.php";
require_once "src/Controllers/Router.php";
require_once "src/Controllers/Home.php";
require_once "src/Models/Friends.php";
require_once "src/Models/User.php";
require_once "src/Views/Authentication.php";
require_once "src/Views/Home.php";
require_once "src/Views/User.php";

$friend_status = array(
  "approved" => "approved",
  "pending_approval" => "pending approval",
  "request_sent" => "request sent",
  "unacquainted" => "unacquainted"
);
$friend_action = array(
  "add" => "add",
  "approve" => "approve",
  "remove" => "remove"
);

$database = new Database();
$encryptor = new Encryptor();

$friends_model = new Friends();
$user_model = new User();

$authentication_view = new AuthenticationView();
$home_view = new HomeView();
$user_view = new UserView();

$home_controller = new Home();
$users_controller = new Users();

$router = new Router();

$database->initConnection();

$user_model->setDb($database);
$friends_model->setDb($database);

// $friends_model->drop();
// $user_model->drop();
// $user_model->createScheme();
// $user_model->populate();
// $friends_model->createScheme();
// $friends_model->populate();
// $database->closeConnection();
// echo "Database has been reset";
// exit(0);

$home_controller->setView($home_view);

$users_controller->setAuthenticationView($authentication_view);
$users_controller->setEncryptor($encryptor);
$users_controller->setFriendsModel($friends_model);
$users_controller->setModel($user_model);
$users_controller->setView($user_view);

$router->setUsersController($users_controller);
$router->setHomeController($home_controller);

$response = $router->run();

$database->closeConnection();

echo $response;

?>