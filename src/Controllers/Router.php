<?php

class Router {
  
  private $_footer;
  private $_header;
  private $_users;
  private $_home;

  public function run() {
    $this->_setHeader();
    $this->_setFooter();
    $error = null;
    $req_uri = $_SERVER['REQUEST_URI'];
    $req_method = $_SERVER['REQUEST_METHOD'];
    $result = $this->_header;
    if(preg_match('#^/$#', $req_uri) && $req_method == "GET") {
      // TODO: CSS
      return $this->_home->htmlContainer();
    }
    else if(preg_match("#^/login$#", $req_uri) && $req_method == "GET") {
      // TODO: CSS
      $result = $result . $this->_loginView();
      return $result . $this->_footer;
    }
    else if(preg_match("#^/login$#", $req_uri) && $req_method == "POST") {
      // TODO: CSS
      $result = $this->_loginToAccount(file_get_contents('php://input'));
      if(isset($result->id)) {
        return "{\"id\":$result->id}";
      } else {
        return "{\"error\":\"$result->error\"}";
      }
    }
    
    else if(preg_match("#^/signup$#", $req_uri) && $req_method == "GET") {
      // TODO: CSS
      $result = $result . $this->_signupView();
      return $result . $this->_footer;
    }
    else if(preg_match("#^/signup$#", $req_uri) && $req_method == "POST") {
      // TODO: CSS
      $result = $this->_createAccount(file_get_contents('php://input'));
      if(isset($result->id)) {
        return "{\"id\":$result->id}";
      } else {
        return "{\"error\":\"$result->error\"}";
      }
    }
    
    else if(preg_match("#^/users/[0-9]+$#", $req_uri) && $req_method == "POST") {
      // TODO: Allow posts
      $user_view = $this->_userView($req_uri, file_get_contents('php://input'));
      if(isset($user_view->status) && isset($user_view->reason) && isset($user_view->message)) {
        return json_encode($user_view);
      }
      $result = $result . $user_view;
      $result = $result . "<div id=\"others_container\">";
      $other_users_list = $this->_otherUsersList(
        $req_uri . "/friends/new/",
        file_get_contents('php://input')
      );
      $result = $result . $other_users_list . "</div>";
      return $result . $this->_footer;
    }
    else if(preg_match("#^/users/[0-9]+/friends/[0-9]+$#", $req_uri) && $req_method == "POST") {
      // TODO: Show posts
      $friend_view = $this->_userFriendView($req_uri, file_get_contents('php://input'));
      if(isset($friend_view->status) &&
        isset($friend_view->reason) &&
        isset($friend_view->message)
        ) {
          return json_encode($friend_view);
      }
      return $this->_header . $friend_view . $this->_footer;
    }
    else if(
      preg_match("#^/users/[0-9]+/friends/add/[0-9]+$#", $req_uri) && $req_method == "POST"
      ) {
        $response = $this->_addFriend($req_uri, file_get_contents('php://input'));
        if(isset($response->status) &&
            isset($response->reason) &&
            isset($response->message)
          ) {
            return json_encode($response);
        }
        return $response;
    }
    else if(
      preg_match("#^/users/[0-9]+/friends/new/[a-z|A-Z|]*[%20]*[a-z|A-Z]*$#", $req_uri) &&
      $req_method == "POST"
      ) {
        // TODO: CSS
        $other_users_list = $this->_otherUsersList($req_uri, file_get_contents('php://input'));
        if(isset($other_users_list->status) &&
          isset($other_users_list->reason) &&
          isset($other_users_list->message)
          ) {
            return json_encode($other_users_list);
        }
        return $other_users_list;
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

  public function setHomeController($controller) {
    $this->_home = $controller;
  }

  private function _addFriend($req_uri, $payload) {
    try {
      $result = null;
      if($this->_users->authorized($payload)) {
        $result = $this->_users->addOrApproveFriend($req_uri);
        return $result;
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at updating relationship with the user";
      return $error;
    }
  }

  private function _createAccount($payload) {
    return $this->_users->new($payload);
  }

  private function _loginToAccount($payload) {
    return $this->_users->existing($payload);
  }

  private function _otherUsersList($req_uri, $payload) {
    try {
      $result = null;
      if($this->_users->authorized($payload)) {
        $result = $this->_users->htmlContainerOthers($req_uri);
        return $result;
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at pulling filtered users";
      return $error;
    }
  }

  private function _setFooter() {
    $result = "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/rc4_encryption.js\"></script>";
    $result = $result . "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/filter_users.js\"></script>";
    $result = $result . "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/relationship.js\"></script>";
    $result = $result . "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/authentication.js\"></script>";
    $this->_footer = $result . "</div></body></html>";
  }

  private function _setHeader() {
    $result = "<html><head><script type=\"text/javascript\" src=\"/src/scripts/components.js\">";
    $this->_header = $result . "</script></head><body><div id=\"application\">";
  }

  private function _userFriendView($req_uri, $payload) {
    // Tested
    try {
      $result = null;
      if($this->_users->authorized($payload)) {
        $result = $this->_users->htmlContainerFriend($req_uri);
        return $result;
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at loading user friend page";
      return $error;
    }
  }

  private function _userView($req_uri, $payload) {
    // Tested
    try {
      $result = null;
      if($this->_users->authorized($payload)) {
        $result = $this->_users->htmlContainer($req_uri);
        return $result;
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at loading user homepage";
      return $error;
    }
  }

  private function _signupView() {
    try {
      $result = $this->_users->htmlContainerSignup();
      return $result;
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at responding with the signup container";
      return json_encode($error);
    }
  }

  private function _loginView() {
    try {
      $result = $this->_users->htmlContainerLogin();
      return $result;
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at responding with the login container";
      return json_encode($error);
    }
  }
}