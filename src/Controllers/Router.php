<?php
namespace AfekaFace\Controllers;

class Router {
  
  private $_users;

  public function run() {
    $error = null;
    $req_uri = $_SERVER['REQUEST_URI'];
    $req_method = $_SERVER['REQUEST_METHOD'];
    $result = null;
    if(preg_match('#^/$#', $req_uri) && $req_method == "GET") {
      return "home view";
    }

    else if(preg_match("#^/login$#", $req_uri) && $req_method == "GET") {
      return "login view";
    }
    else if(preg_match("#^/login$#", $req_uri) && $req_method == "POST") {
      return "login action";
    }
    
    else if(preg_match("#^/signup$#", $req_uri) && $req_method == "GET") {
      return "signup view";
    }
    else if(preg_match("#^/signup$#", $req_uri) && $req_method == "POST") {
      return "signup action";
    }
    
    else if(preg_match("#^/users/[0-9]+$#", $req_uri) && $req_method == "GET") {
      try {
        $result = $this->_users->htmlContainer($req_uri);
        return $result;
      } catch(Exception $err) {
        $error->status = "Error";
        $error->reason = $err->getMessage();
        $error->message = "Failed to at GET {$req_uri}";
      }
      return json_encode($error);
    }
    else if(preg_match("#^/users/[0-9]+/friends/new$#", $req_uri) && $req_method == "GET") {
      return "user id friends new view";
    }
    else if(preg_match("#^/users/[0-9]+/friends/add$#", $req_uri) && $req_method == "POST") {
      return "user id friends add action";
    }
    else if(preg_match("#^/users/[0-9]+/friends/remove$#", $req_uri) && $req_method == "POST") {
      return "user id friends remove action";
    }
    else if(preg_match("#^/users/[0-9]+/friends/[0-9]+$#", $req_uri) && $req_method == "POST") {
      return "user id friends id view";
    }

    else if(
      preg_match("#^/users/[0-9]+/friends/[0-9]+/posts$#", $req_uri) &&
      $req_method == "GET"
      ) {
        return "user id friends posts view";
    }
    else if(
      preg_match("#^/users/[0-9]+/friends/[0-9]+/posts/[0-9]+$#", $req_uri) &&
      $req_method == "GET"
      ) {
        return "user id friends posts id view";
    }

    else if(preg_match("#^/users/[0-9]+/posts$#", $req_uri) && $req_method == "GET") {
      return "user id posts view";
    }
    else if(preg_match("#^/users/[0-9]+/posts/[0-9]+$#", $req_uri) && $req_method == "GET") {
      return "user id post id view";
    }
    else {
      return "page not found";
    }
  }

  public function setUsersController($controller) {
    $this->_users = $controller;
  }
}