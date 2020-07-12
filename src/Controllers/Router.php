<?php
namespace AfekaFace\Controllers;

class Router {
  
  private $_users;
  private $_header;
  private $_footer;

  public function run() {
    $this->_setHeader();
    $this->_setFooter();
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
      $result = $this->_header;
      $result = $result . $this->_userView($req_uri);
      $result = $result . "<div id=\"others_container\">";
      $result = $result . $this->_otherUsersList($req_uri . "/friends/new/") . "</div>";
      return $result . $this->_footer;
    }
    else if(preg_match("#^/users/[0-9]+/friends/[0-9]+$#", $req_uri) && $req_method == "GET") {
      return $this->_header . $this->_userFriendView($req_uri) . $this->_footer;
    }
    else if(preg_match("#^/users/[0-9]+/friends/add/[0-9]+$#", $req_uri) && $req_method == "GET") {
      return "user id friends add action";
    }
    else if(
      preg_match("#^/users/[0-9]+/friends/new/[a-z|A-Z|]*[%20]*[a-z|A-Z]*$#", $req_uri) &&
      $req_method == "GET"
      ) {
        return $this->_otherUsersList($req_uri);
    }
    else if(
      preg_match("#^/users/[0-9]+/friends/remove/[0-9]+$#", $req_uri) &&
      $req_method == "GET"
      ) {
        return "user id friends remove action";
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

  private function _otherUsersList($req_uri) {
    try {
      $result = $this->_users->htmlContainerOthers($req_uri);
      return $result;
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at GET {$req_uri}";
      return json_encode($error);
    }
  }

  private function _setFooter() {
    $result = "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/filter_users.js\"></script>";
    $this->_footer = $result . "</body></html>";
  }

  private function _setHeader() {
    $result = "<html><head></head></html>";
    $this->_header = $result;
  }

  private function _userFriendView($req_uri) {
    try {
      $result = $this->_users->htmlContainerFriend($req_uri);
      return $result;
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at GET {$req_uri}";
      return json_encode($error);
    }
  }

  private function _userView($req_uri) {
    try {
      $result = $this->_users->htmlContainer($req_uri);
      return $result;
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at GET {$req_uri}";
      return json_encode($error);
    }
  }
}