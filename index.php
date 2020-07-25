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

// STATIC VARIABLES (Enums like behavior)
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

// Additional variables
$error = null;

// Initialization of the objects, i.e. models, controllers, utilities etc.
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

// Initialize database (i.e. connection, inject into the models, reset database)
try {
  $database->initConnection();
} catch(Exception $err) {
  $error->status = "Error";
  $error->reason = $err->getMessage();
  $error->message = "Failed to connect to the database";
  echo(json_encode($error));
  exit(1);
}

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

// Inject models, views to the controllers
$home_controller->setView($home_view);

$users_controller->setAuthenticationView($authentication_view);
$users_controller->setEncryptor($encryptor);
$users_controller->setFriendsModel($friends_model);
$users_controller->setModel($user_model);
$users_controller->setView($user_view);

$router->setUsersController($users_controller);
$router->setHomeController($home_controller);

// Start listening to the requests
$response = $router->run();

try {
  $database->closeConnection();
} catch(Exception $err) {
  $error->status = "Error";
  $error->reason = $err->getMessage();
  $error->message = "Failed to close the connection to the database";
  echo(json_encode($error));
  exit(1);
}

echo ($response);

?>