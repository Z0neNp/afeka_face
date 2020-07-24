<?php

class Users {
  
  private $_encryptor;
  private $_model_friends;
  private $_model;
  private $_view;
  private $_view_authentication;

  public function addOrApproveFriend($req_uri) {
    $error = null;
    $friend_id = null;
    $relationship_status = null;
    $result = null;
    $user_id = null;
    try {
      $user_id = $this->_userIdFromReqUri($req_uri);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrieve the user_id from the request URI";
      return json_encode($error);
    }
    try {
      $friend_id = $this->_friendIdFromReqUriForRelationshipStatus($req_uri);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrieve the user_id from the request URI";
      return json_encode($error);
    }
    try {
      $relationship_status = $this->_model_friends->status($user_id, $friend_id);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrieve the relationship status from the database";
      return json_encode($error);
    }
    try {
      if($relationship_status == $GLOBALS["friend_status"]["unacquainted"]) {
        $this->_model_friends->statusToRequestSent($user_id, $friend_id);
        $result = "changed";
      }
      else if($relationship_status == $GLOBALS["friend_status"]["pending_approval"]) {
        $this->_model_friends->statusToApproved($user_id, $friend_id);
        $result = "changed";
      }
      else {
        $result = "unchanged";
      }
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to update the relationship status in the database";
      return json_encode($error);
    }
    return $result;
  }
  
  public function authorized($rc4_encrypted_data) {
    $credentials_raw = $this->_encryptor->decrypt($rc4_encrypted_data);
    $credentials = json_decode($credentials_raw);
    if($this->_model->isPersisted($credentials->first_name, $credentials->last_name)) {
      $user_id = $this->_model->userId(
        $credentials->first_name,
        $credentials->last_name,
        $rc4_encrypted_data
      );
      return isset($user_id);
    }
    return false;
  }

  public function existing($rc4_encrypted_data) {
    $result = null;
    $credentials_raw = $this->_encryptor->decrypt($rc4_encrypted_data);
    $credentials = json_decode($credentials_raw);
    if($this->_model->isPersisted($credentials->first_name, $credentials->last_name)) {
      $user_id = $this->_model->userId(
        $credentials->first_name,
        $credentials->last_name,
        $rc4_encrypted_data
      );
      if(isset($user_id)) {
        $result->id = $user_id;
      }
      else {
        $result->error = "Wrong password";
      }
    }
    else {
      $result->error = "No user has the provided information.";
    }
    return $result;
  }

  public function htmlContainer($req_uri) {
    $error = null;
    $friends_data = null;
    $relationships = null;
    $result = null;
    $user = null;
    $user_id = null;
    try {
      $user_id = $this->_userIdFromReqUri($req_uri);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrieve the user_id from the request URI";
      return json_encode($error);
    }
    try {
      $user = $this->_model->details($user_id);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "The retrieved user data from the database was corrupted";
      return json_encode($error);
    }
    try {
      $relationships = $this->_model_friends->all($user_id);
      if(!$this->_friendsRelationshipsLegal($relationships)) {
        throw new Exception("Illegal friends relationship data");
      }
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "The retrieved friends relationship data from the database was corrupted";
      return json_encode($error);
    }
    try {
      $friends_data = $this->_friendsData($relationships);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "The retrieved friends data from the database was corrupted";
      return json_encode($error);
    }
    if($this->_userLegal($user) && $this->_friendsDataLegal($friends_data)) {
      try {
        $result = $this->_view->view($user, $friends_data);
        return $result;
      } catch(Exception $err) {
        $error->status = "Error";
        $error->reason = $err->getMessage();
        $error->message = "Failed to build a user view html container";
        return json_encode($error);
      }
    }
    $error->status = "Error";
    $error->message = "User data or user's friends data retrieved from the database was illegal";
    return json_encode($error);
  }

  public function htmlContainerFriend($req_uri) {
    $error = null;
    $friend = null;
    $friend_id = null;
    $result = null;
    $user_id = null;
    try {
      $user_id = $this->_userIdFromReqUri($req_uri);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrieve the user_id from the request URI";
      return json_encode($error);
    }
    try {
      $friend_id = $this->_friendIdFromReqUri($req_uri);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrieve the friend_id from the request URI";
      return json_encode($error);
    }
    try {
      $friend = $this->_model->details($friend_id);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "The retrieved friend data from the database was corrupted";
      return json_encode($error);
    }
    if($this->_userLegal($friend)) {
      try {
        $result = $this->_view->viewFriend($user_id, $friend);
        return $result;
      } catch(Exception $err) {
        $error->status = "Error";
        $error->reason = $err->getMessage();
        $error->message = "Failed to build a user view html container";
        return json_encode($error);
      }
    }
    $error->status = "Error";
    $error->message = "User data or user's friends data retrieved from the database was illegal";
    return json_encode($error);
  }

  public function htmlContainerLogin() {
    return $this->view_authentication->containerLogin();
  }

  public function htmlContainerOthers($req_uri) {
    $error = null;
    $filter = null;
    $others = null;
    $result = null;
    $user_id = null;
    try {
      $user_id = $this->_userIdFromReqUri($req_uri);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrieve the user_id from the request URI";
      return json_encode($error);
    }
    try {
      $filter = $this->_usersFilterFromReqURI($req_uri);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrieve the filter string from the request URI";
      return json_encode($error);
    }
    try {
      $others = $this->_model->others($user_id, $filter);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrieve other users based on the filter string";
      json_encode($error);
    }
    try {
      foreach($others as $other) {
        if($this->_userLegal($other)) {
          $status = $this->_model_friends->status($user_id, $other->id);
          if($status == $GLOBALS["friend_status"]["approved"] || 
              $status == $GLOBALS["friend_status"]["request_sent"]
            ) {
              $other->actions = array($GLOBALS["friend_action"]["remove"]);
          }
          else if($status == "pending approval") {
            $other->actions = array(
              $GLOBALS["friend_action"]["add"],
              $GLOBALS["friend_action"]["remove"]
            );
          }
          else {
            $other->actions = array($GLOBALS["friend_action"]["add"]);
          }
        }
        else {
          $error->status = "Error";
          $error->message = "One of the other users had a corrupted data";
          return json_encode($error);
        }
      }
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "One of the other users had a corrupted data";
      return json_encode($error);
    }
    try {
      return $this->_view->othersList($user_id, $others);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to build other users list";
      return json_encode($error);
    }
  }

  public function htmlContainerSignup() {
    return $this->view_authentication->containerSignup();
  }

  public function new($rc4_encrypted_data) {
    $result = null;
    $credentials_raw = $this->_encryptor->decrypt($rc4_encrypted_data);
    $credentials = json_decode($credentials_raw);
    if($this->_model->isPersisted($credentials->first_name, $credentials->last_name)) {
      $result->error = "user exists";
    }
    else {
      $user_id = $this->_model->new(
        $credentials->first_name,
        $credentials->last_name,
        $credentials->password
      );
      if(isset($user_id)) {
        $result->id = $user_id;
      }
      else {
        $result->error = "Failed to persist the new user";
      }
    }
    return $result;
  }

  public function setFriendsModel($model) {
    $this->_model_friends = $model;
  }

  public function setModel($model) {
    $this->_model = $model;
  }

  public function setAuthenticationView($view) {
    $this->view_authentication = $view;
  }

  public function setEncryptor($encryptor) {
    $this->_encryptor = $encryptor;
  }

  public function setView($view) {
    $this->_view = $view;
  }

  private function _friendsData($relationships) {
    $result = array();
    foreach($relationships as $relationship) {
      $next = null;
      $next = $this->_model->details($relationship->id);
      $next->status = $relationship->status;
      array_push($result, $next);
    }
    return $result;
  }

  private function _friendsDataLegal($friends) {
    if(!isset($friends)) {
      return false;
    }
    foreach($friends as $friend) {
      if(!$this->_userLegal($friend) || !isset($friend->status)) {
        return false;
      }
    }
    return true;
  }

  private function _friendIdFromReqUri($req_uri) {
    // expected URI is /users/[0-9]+/friends/[0-9]+
    $splitted = explode("/", $req_uri);
    return intval($splitted[4]);
  }

  private function _friendIdFromReqUriForRelationshipStatus($req_uri) {
    $splitted = explode("/", $req_uri);
    return intval($splitted[5]);
  }

  private function _friendsRelationshipsLegal($relationships) {
    if(isset($relationships)) {
      foreach($relationships as $relationship) {
        if(!isset($relationship->id) || !isset($relationship->status)) {
          return false;
        }
      }
      return true;
    }
    return false;
  }

  private function _usersFilterFromReqURI($req_uri) {
    // expected URI is /users/[0-9]+/friends/new/[a-z|A-Z|]+%20[a-z|A-Z]+
    $splitted = explode("/", $req_uri);
    $result = preg_replace('/%20/', ' ', $splitted[5]);
    return $result;
  }

  private function _userLegal($user) {
    if(isset($user)) {
      return isset($user->first_name) && isset($user->last_name);
    }
    return false;
  }

  private function _userIdFromReqUri($req_uri) {
    // expected URI is /users/[0-9]+
    $splitted = explode("/", $req_uri);
    return intval($splitted[2]);
  }

  private function _rc4EncryptedDataFromPayload($payload) {
    $result = null;
    $result->key = substr($payload, 0, 5);
    $result->credentials = substr($payload, 5);
    return $result;
  }

}

?>