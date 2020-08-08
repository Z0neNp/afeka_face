<?php

class Router {
  private $_users;
  private $_home;

  private $_user_model;
  private $_friends_model;
  private $_post_model;
  private $_picture_model;

  public function run() {
    $controller_response = null;
    $req_method = $_SERVER['REQUEST_METHOD'];
    $req_uri = $_SERVER['REQUEST_URI'];
    $partial_view_response = false;
    if($this->_routeToHomePage($req_uri, $req_method)) {
      $controller_response = $this->_home->htmlContainer();
    }
    else if($this->_routeToLoginPage($req_uri, $req_method)) {
      $controller_response = $this->_loginView();
      $partial_view_response = true;
    }
    else if($this->_routeToLoginAction($req_uri, $req_method)) {
      $controller_response = $this->_loginToAccount(file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToSignupPage($req_uri, $req_method)) {
      $controller_response = $this->_signupView();
      $partial_view_response = true;
    }
    else if($this->_routeToSignupAction($req_uri, $req_method)) {
      $controller_response = $this->_createAccount(file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToUserHomepage($req_uri, $req_method)) {
      $controller_response = $this->_userView($req_uri, file_get_contents('php://input'));
      if(!$this->_controllerRespondedWithError($controller_response)) {
        $other_users_list = $this->_otherUsersList(
          $req_uri . "/friends/new/",
          file_get_contents('php://input')
        );
        if(!$this->_controllerRespondedWithError($other_users_list)) {
          $controller_response = $controller_response . "<div id=\"others_container\">";
          $controller_response = $controller_response . $other_users_list ."</div>";
        }
      }
      $partial_view_response = true;
    }
    else if($this->_routeToUserFriend($req_uri, $req_method)) {
      $controller_response = $this->_userFriendView($req_uri, file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToUserAddFriendAction($req_uri, $req_method)) {
      $controller_response = $this->_addFriend($req_uri, file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToUserFilterOtherUsers($req_uri, $req_method)) {
      $controller_response = $this->_otherUsersList($req_uri, file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToUserRemoveFriendAction($req_uri, $req_method)) {
      $controller_response = $this->_removeFriend($req_uri, file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToUserPosts($req_uri, $req_method)) {
      $controller_response = $this->_posts($req_uri, file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToUserPostForm($req_uri, $req_method)) {
      $controller_response = $this->_postsNewForm($req_uri, file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToUserPostAddAction($req_uri, $req_method)) {
      $controller_response = $this->_postsNewAdd($req_uri, file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToUserFriendPosts($req_uri, $req_method)) {
      $controller_response = $this->_postsFriend($req_uri, file_get_contents('php://input'));
      $partial_view_response = true;
    }
    else if($this->_routeToDatabaseReset($req_uri, $req_method)) {
      $controller_response = $this->_resetDatabase();
      $partial_view_response = true;
    }
    else {
      $response->status = "Error";
      $response->reason = "Page not found";
      return json_encode($response);
    }
    if($this->_controllerRespondedWithError($controller_response)) {
      return json_encode($controller_response);
    }
    else if($partial_view_response) {
      return $controller_response;
    }
    else {
      return $this->_genHeader() . $controller_response . $this->_genFooter();
    }
  }

  public function setHomeController($controller) {
    $this->_home = $controller;
  }

  public function setModelFriends($model) {
   $this->_friends_model = $model; 
  }

  public function setModelPicture($model) {
    $this->_picture_model = $model; 
  }

  public function setModelPost($model) {
    $this->_post_model = $model; 
  }

  public function setModelUser($model) {
    $this->_user_model = $model; 
  }

  public function setUsersController($controller) {
    $this->_users = $controller;
  }

  public function setPostView($view) {
    $this->_view_post = $view;
  }

  private function _bootstrapCssLink() {
    $result = "<link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.0";
    $result = $result . "/css/bootstrap.min.css\" integrity=\"sha384-9aIt2nRpC12Uk9gS9baDl411NQAp";
    $result = $result . "FmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk\" crossorigin=\"anonymous\">";
    $result = $result . "<script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/";
    $result = $result . "bootstrap.min.js\" integrity=\"sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835";
    $result = $result . "Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI\" crossorigin=\"anonymous\"></script>";
    return $result;
  }

  private function _bootstrapJsLink() {
    $result = "<script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\"";
    $result = $result . "integrity=\"sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamo";
    $result = $result . "FVy38MVBnE+IbbVYUew+OrCXaRkfj\" crossorigin=\"anonymous\"></script>";
    $result = $result . "<script src=\"https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd";
    $result = $result . "/popper.min.js\" integrity=\"sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9I";
    $result = $result . "OYy5n3zV9zzTtmI3UksdQRVvoxMfooAo\" crossorigin=\"anonymous\"></script>";
    return $result;
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

  private function _controllerRespondedWithError($response) {
    return isset($view_response->status) &&
      isset($view_response->reason) &&
      isset($login_view->message);
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

  private function _genFooter() {
    $result = "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/rc4_encryption.js\"></script>";
    $result = $result . "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/filter_users.js\"></script>";
    $result = $result . "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/relationship.js\"></script>";
    $result = $result . "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/authentication.js\"></script>";
    $result = $result . "<script type=\"text/javascript\"";
    $result = $result . "src=\"/src/scripts/posts.js\"></script>";
    $result = $result . "</div></body></html>";
    return $result;
  }

  private function _genHeader() {
    $result = "<html><head><script type=\"text/javascript\" src=\"/src/scripts/components.js\">";
    $result = $result . "</script><meta name=\"viewport\" content=\"width=device-width, ";
    $result = $result . "initial-scale=1, shrink-to-fit=no\">" . $this->_bootstrapJsLink();
    $result = $result . $this->_bootstrapCssLink();
    $result = $result . "</head><body><div id=\"application\" class=\"containter\">";
    return $result;
  }

  private function _normalizeCredentials($credentials) {
    $result = null;
    foreach($credentials as $encrypted) {
      if(isset($result)) {
        $result = $result . ",$encrypted";
      }
      else {
        $result = $encrypted;
      }
    }
    return $result;
  }

  private function _normalizePost($post_json) {
    $result->message = $post_json->{"message"};
    $result->private = false;
    if(isset($post_json->{"message"})) {
      $result->message = $post_json->{"message"};
    }
    if(isset($post_json->{"thumbnail"})) {
      $result->thumbnail = $post_json->{"thumbnail"};
    }
    if(isset($post_json->{"images"})) {
      $result->pictures = $post_json->{"images"};
    }
    if($post_json->{"private"} == $GLOBALS["post_visibility"]["private"]) {
      $result->private = true;
    }
    return $result;
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

  private function _posts($req_uri, $payload) {
    try {
      if($this->_users->authorized($payload)) {
        return $this->_users->htmlContainerPosts($req_uri);
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at pulling user posts";
      return $error;
    }
  }

  private function _postsFriend($req_uri, $payload) {
    try {
      if($this->_users->authorized($payload)) {
        return $this->_users->htmlContainerPostsFriend($req_uri);
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at pulling user's friend posts";
      return $error;
    }
  }

  private function _postsNewAdd($req_uri, $payload) {
    try {
      $client_data = json_decode($payload);
      if($this->_users->authorized($this->_normalizeCredentials($client_data->{"credentials"}))) {
        return $this->_users->newPost($req_uri, $this->_normalizePost($client_data->{"post"}));
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at pulling the html container with the new post form";
      return $error;
    }
    print_r($client_data->{"credentials"});
  }

  private function _postsNewForm($req_uri, $payload) {
    try {
      if($this->_users->authorized($payload)) {
        return $this->_users->htmlContainerPostNewForm($req_uri, $req_uri);
      }
      throw new Exception("You are not authorized.\nPlease login.");
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at pulling the html container with the new post form";
      return $error;
    }
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

  private function _resetDatabase() {
    try {
      $this->_friends_model->drop();
      $this->_picture_model->drop();
      $this->_post_model->drop();
      $this->_user_model->drop();
      $this->_user_model->createScheme();
      $this->_user_model->populate();
      $this->_friends_model->createScheme();
      $this->_friends_model->populate();
      $this->_post_model->createScheme();
      $this->_post_model->populate();
      $this->_picture_model->createScheme();
      $this->_picture_model->populate();
      return "Database has been reset";
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed at resetting the database";
      return $error;
    }
  }

  private function _routeToDatabaseReset($req_uri, $req_method) {
    return preg_match("#^/reset_database$#", $req_uri) && $req_method == "GET";
  }

  private function _routeToHomePage($req_uri, $req_method) {
    return preg_match('#^/$#', $req_uri) && $req_method == "GET";
  }

  private function _routeToLoginAction($req_uri, $req_method) {
    return preg_match("#^/login$#", $req_uri) && $req_method == "POST";
  }

  private function _routeToLoginPage($req_uri, $req_method) {
    return preg_match("#^/login$#", $req_uri) && $req_method == "GET";
  }
  
  private function _routeToSignupAction($req_uri, $req_method) {
    return preg_match("#^/signup$#", $req_uri) && $req_method == "POST";
  }

  private function _routeToSignupPage($req_uri, $req_method) {
    return preg_match("#^/signup$#", $req_uri) && $req_method == "GET";
  }

  private function _routeToUserHomepage($req_uri, $req_method) {
    return preg_match("#^/users/[0-9]+$#", $req_uri) && $req_method == "POST";
  }

  private function _routeToUserAddFriendAction($req_uri, $req_method) {
    return preg_match("#^/users/[0-9]+/friends/add/[0-9]+$#", $req_uri) && $req_method == "POST";
  }

  private function _routeToUserFilterOtherUsers($req_uri, $req_method) {
    return preg_match("#^/users/[0-9]+/friends/new/[a-z|A-Z|]*[%20]*[a-z|A-Z]*$#", $req_uri) &&
      $req_method == "POST";
  }

  private function _routeToUserFriend($req_uri, $req_method) {
    return preg_match("#^/users/[0-9]+/friends/[0-9]+$#", $req_uri) && $req_method == "POST";
  }

  private function _routeToUserFriendPosts($req_uri, $req_method) {
    return preg_match("#^/users/[0-9]+/friends/[0-9]+/posts$#", $req_uri) && $req_method == "POST";
  }

  private function _routeToUserPosts($req_uri, $req_method) {
    return preg_match("#^/users/[0-9]+/posts$#", $req_uri) && $req_method == "POST";
  }

  private function _routeToUserPostAddAction($req_uri, $req_method) {
    return preg_match("#^/users/[0-9]+/posts/add$#", $req_uri) && $req_method == "POST";
  }
  
  private function _routeToUserPostForm($req_uri, $req_method) {
    return preg_match("#^/users/[0-9]+/posts/new$#", $req_uri) && $req_method == "POST";
  }

  private function _routeToUserRemoveFriendAction($req_uri, $req_method) {
    return preg_match("#^/users/[0-9]+/friends/remove/[0-9]+$#", $req_uri) &&
      $req_method == "POST";
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