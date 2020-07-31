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
    $req_method = $_SERVER['REQUEST_METHOD'];
    $req_uri = $_SERVER['REQUEST_URI'];
    $result = $this->_header;
    if(preg_match('#^/$#', $req_uri) && $req_method == "GET") {
      return $result . $this->_home->htmlContainer() . $this->_footer;
    }
    else if(preg_match("#^/login$#", $req_uri) && $req_method == "GET") {
      $login_view = $this->_loginView();
      if(isset($login_view->status) && isset($login_view->reason) && isset($login_view->message)) {
        return json_encode($login_view);
      }
      return $result . $login_view . $this->_footer;
    }
    else if(preg_match("#^/login$#", $req_uri) && $req_method == "POST") {
      $response = $this->_loginToAccount(file_get_contents('php://input'));
      if(isset($response->status) && isset($response->reason) && isset($response->message)) {
        return json_encode($response);
      }
      return $response;
    }
    
    else if(preg_match("#^/signup$#", $req_uri) && $req_method == "GET") {
      $singupView = $this->_signupView();
      if(isset($singupView->status) && isset($singupView->reason) && isset($singupView->message)) {
        return json_encode($singupView);
      }
      return $result . $singupView . $this->_footer;
    }
    else if(preg_match("#^/signup$#", $req_uri) && $req_method == "POST") {
      $response = $this->_createAccount(file_get_contents('php://input'));
      if(isset($response->status) && isset($response->reason) && isset($response->message)) {
        return json_encode($response);
      }
      return $response;
    }
    
    else if(preg_match("#^/users/[0-9]+$#", $req_uri) && $req_method == "POST") {
      // TODO: Allow posts
      $user_view = $this->_userView($req_uri, file_get_contents('php://input'));
      if(isset($user_view->status) && isset($user_view->reason) && isset($user_view->message)) {
        return json_encode($user_view);
      }
      $result = $result . $user_view . "<div id=\"others_container\">";
      $other_users_list = $this->_otherUsersList(
        $req_uri . "/friends/new/",
        file_get_contents('php://input')
      );
      if(isset($other_users_list->status) &&
          isset($other_users_list->reason) &&
          isset($other_users_list->message)
        ) {
          return json_encode($other_users_list);
      }
      return $result . $other_users_list . "</div>" . $this->_footer;
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
      $req_method == "POST"
      ) {
        $response = $this->_removeFriend($req_uri, file_get_contents('php://input'));
        if(isset($response->status) &&
            isset($response->reason) &&
            isset($response->message)
          ) {
            return json_encode($response);
        }
        return $response;
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

  private function _bootstrapCssLink() {
    $result = "<link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.0";
    $result = $result . "/css/bootstrap.min.css\" integrity=\"sha384-9aIt2nRpC12Uk9gS9baDl411NQAp";
    return $result . "FmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk\" crossorigin=\"anonymous\">";
  }

  private function _bootstrapJsLink() {
    $result = "<script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\"";
    $result = $result . "integrity=\"sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamo";
    $result = $result . "FVy38MVBnE+IbbVYUew+OrCXaRkfj\" crossorigin=\"anonymous\"></script>";
    $result = $result . "<script src=\"https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd";
    $result = $result . "/popper.min.js\" integrity=\"sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9I";
    $result = $result . "OYy5n3zV9zzTtmI3UksdQRVvoxMfooAo\" crossorigin=\"anonymous\"></script>";
    $result = $result . "<script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/";
    $result = $result . "bootstrap.min.js\" integrity=\"sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835";
    return $result . "Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI\" crossorigin=\"anonymous\"></script>";
  }

  private function _addFriend($req_uri, $payload) {
    try {
      if($this->_users->authorized($payload)) {
        return $this->_users->addOrApproveFriend($req_uri);
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
    try {
      $result = $this->_users->new($payload);
      if(isset($result->id)) {
        return "{\"id\":$result->id}";
      } else {
        return "{\"error\":\"$result->error\"}";
      }
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at creating new account";
      return $error;
    }
  }

  private function _loginToAccount($payload) {
    try {
      $result = $this->_users->existing($payload);
      if(isset($result->id)) {
        return "{\"id\":$result->id}";
      } else {
        return "{\"error\":\"$result->error\"}";
      }
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at loggin into the existing account";
      return $error;
    }
  }

  private function _loginView() {
    try {
      return $this->_users->htmlContainerLogin();
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at generating the login container";
      return json_encode($error);
    }
  }

  private function _otherUsersList($req_uri, $payload) {
    try {
      if($this->_users->authorized($payload)) {
        return $this->_users->htmlContainerOthers($req_uri);
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
    $result = $result . $this->_bootstrapJsLink() . "</div></body></html>";
    $this->_footer = $result;
  }

  private function _setHeader() {
    $result = "<html><head><script type=\"text/javascript\" src=\"/src/scripts/components.js\">";
    $result = $result . "</script><meta name=\"viewport\" content=\"width=device-width, ";
    $result = $result . "initial-scale=1, shrink-to-fit=no\">" . $this->_bootstrapCssLink();
    $result = $result . "</head><body>";
    $this->_header = $result . "<div id=\"application\" class=\"containter\">";
  }

  private function _removeFriend($req_uri, $payload) {
    try {
      if($this->_users->authorized($payload)) {
        return $this->_users->removeFriend($req_uri);
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at updating relationship with the user";
      return $error;
    }
  }

  private function _signupView() {
    try {
      return $this->_users->htmlContainerSignup();
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at responding with the signup container";
      return json_encode($error);
    }
  }

  private function _userView($req_uri, $payload) {
    try {
      if($this->_users->authorized($payload)) {
        return $this->_users->htmlContainer($req_uri);
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at loading user homepage";
      return $error;
    }
  }

  private function _userFriendView($req_uri, $payload) {
    try {
      if($this->_users->authorized($payload)) {
        return $this->_users->htmlContainerFriend($req_uri);
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at loading user friend page";
      return $error;
    }
  }
}

?>