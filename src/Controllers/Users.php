<?php

namespace AfekaFace\Controllers;

class Users {
  
  private $_model;
  private $_view;

  public function homePage($req_uri) {
    $error = null;
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
      if($this->_userLegal($user)) {
        try {
          $result = $this->_view->home($user);
          return $result;
        } catch(Exception $err) {
          $error->status = "Error";
          $error->reason = $err->getMessage();
          $error->message = "Failed to build a user home page";
          return json_decode($error);
        }
      }
      $error->status = "Error";
      $error->message = "The retrieved user data from the database was corrupted";
      return json_encode($error);
    } catch(Exception $err) {
      $error->status = "Error";
      $error->reason = $err->getMessage();
      $error->message = "Failed to retrive user data from the database";
      return json_encode($error);
    }
  }

  public function setModel($model) {
    $this->_model = $model;
  }

  public function setView($view) {
    $this->_view = $view;
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