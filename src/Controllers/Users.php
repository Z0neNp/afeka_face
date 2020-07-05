<?php

namespace AfekaFace\Controllers;

class Users {
  
  private $_model_friends;
  private $_model;
  private $_view;

  public function htmlContainer($req_uri) {
    $error = null;
    $relationships = null;
    $friends_data = null;
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
        $result = $this->_view->home($user, $friends_data);
        return $result;
      } catch(Exception $err) {
        $error->status = "Error";
        $error->reason = $err->getMessage();
        $error->message = "Failed to build a user home html container";
        return json_encode($error);
      }
    }
    $error->status = "Error";
    $error->message = "User data or user's friends data retrieved from the database was illegal";
    return json_encode($error);
  }

  public function setFriendsModel($model) {
    $this->_model_friends = $model;
  }

  public function setModel($model) {
    $this->_model = $model;
  }

  public function setView($view) {
    $this->_view = $view;
  }

  private function _friendsData($relationships) {
    return array_map(
      function($relationship) {
        $result = $this->_model->details($relationship->id);
        $result->status = $relationship->status;
        return $result;
      },
      $relationships
    );
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

  private function _friendsRelationshipsLegal($relationships) {
    return isset($relationships);
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

}