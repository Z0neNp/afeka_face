<?php

class Users {
  
  private $_encryptor;
  private $_model;
  private $_model_friends;
  private $_model_post;
  private $_model_picture;
  private $_view;
  private $_view_authentication;
  private $_view_post;

  public function addOrApproveFriend($req_uri) {
    $friend_id = $this->_friendIdFromReqUriForRelationshipStatus($req_uri);
    $user_id = $this->_userIdFromReqUri($req_uri);
    $relationship_status = $this->_model_friends->status($user_id, $friend_id);
    $result = "unchanged";
    if($relationship_status == $GLOBALS["friend_status"]["unacquainted"]) {
      $this->_model_friends->statusToRequestSent($user_id, $friend_id);
      $result = "changed";
    }
    else if($relationship_status == $GLOBALS["friend_status"]["pending_approval"]) {
      $this->_model_friends->statusToApproved($user_id, $friend_id);
      $result = "changed";
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
      $result->error = "No user has the given information.";
    }
    return $result;
  }

  public function htmlContainer($req_uri) {
    $user_id = $this->_userIdFromReqUri($req_uri);
    $relationships = $this->_model_friends->all($user_id);
    if(!$this->_friendsRelationshipsLegal($relationships)) {
      throw new Exception("Friends relationship data is illegal");
    }
    $friends_data = $this->_friendsData($relationships);
    $user = $this->_model->details($user_id);    
    if($this->_userLegal($user) && $this->_friendsDataLegal($friends_data)) {
      return $this->_view->view($user, $friends_data);
    }
    throw new Exception("User or friends data retrieved from the database is illegal");
  }

  public function htmlContainerFriend($req_uri) {
    $friend_id = $this->_friendIdFromReqUri($req_uri);
    $friend = $this->_model->details($friend_id);
    $user_id = $this->_userIdFromReqUri($req_uri);
    if($this->_userLegal($friend)) {
      return $this->_view->viewFriend($user_id, $friend);
    }
    throw new Exception("Friend data retrieved from the database was illegal");
  }

  public function htmlContainerLogin() {
    return $this->view_authentication->containerLogin();
  }

  public function htmlContainerOthers($req_uri) {
    $filter = $this->_usersFilterFromReqURI($req_uri);
    $user_id = $this->_userIdFromReqUri($req_uri);
    $others = $this->_model->others($user_id, $filter);
    $this->_updateActionsTowardsOtherUsers($user_id, $others);
    return $this->_view->othersList($user_id, $others);
  }

  public function htmlContainerPostNewForm($req_uri) {
    $user_id = $this->_userIdFromReqUri($req_uri);
    return $this->_view_post->htmlContainerNewForm($user_id);
  }

  public function htmlContainerPosts($req_uri) {
    $user_id = $this->_userIdFromReqUri($req_uri);
    $posts = $this->_posts($user_id);
    return $this->_view_post->htmlContainer($posts);
  }

  public function htmlContainerPostsFriend($req_uri) {
    $friend_id = $this->_friendIdFromReqUri($req_uri);
    $posts = $this->_postsFriend($friend_id);
    return $this->_view_post->htmlContainer($posts);
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

  public function newPost($req_uri, $post) {
    $user_id = $this->_userIdFromReqUri($req_uri);
    $post_id = $this->_model_post->new($user_id, $post->private, $post->message);
    $this->_model_picture->new($user_id, $post_id, $post->thumbnail, "TRUE");
    foreach($post->pictures as $picture) {
      $this->_model_picture->new($user_id, $post_id, $picture, "FALSE");
    }
  }

  public function removeFriend($req_uri) {
    $error = null;
    $friend_id = $this->_friendIdFromReqUriForRelationshipStatus($req_uri);
    $user_id = $this->_userIdFromReqUri($req_uri);
    $relationship_status = $this->_model_friends->status($user_id, $friend_id);
    $result = "unchanged";
    if($relationship_status == $GLOBALS["friend_status"]["approved"] ||
        $relationship_status == $GLOBALS["friend_status"]["pending_approval"] ||
        $relationship_status == $GLOBALS["friend_status"]["request_sent"]  
      ) {
      $this->_model_friends->statusToUnacquainted($user_id, $friend_id);
      $result = "changed";
    }
    return $result;
  }

  public function setFriendsModel($model) {
    $this->_model_friends = $model;
  }

  public function setModel($model) {
    $this->_model = $model;
  }

  public function setPictureModel($model) {
    $this->_model_picture = $model;
  }

  public function setPostModel($model) {
    $this->_model_post = $model;
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

  public function setViewPost($view) {
    $this->_view_post = $view;
  }

  private function _friendsData($relationships) {
    $result = array();
    foreach($relationships as $relationship) {
      $next = null;
      $next = $this->_model->details($relationship->id);
      $next->status = $relationship->status;
      if($relationship->status == $GLOBALS["friend_status"]["approved"]) {
        $next->link = true;
      }
      else {
        $next->link = false;
      }
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

  private function _pictures($user_id, $post_id) {
    $result = array();
    $pictures = $this->_model_picture->picturesBy($user_id, $post_id);
    foreach($pictures as $picture) {
      if(!$picture["thumbnail"]) {
        array_push($result, $picture["url"]);
      }
    }
    return $result;
  }

  private function _posts($user_id) {
    $result = array();
    $posts = $this->_model_post->postsBy($user_id);
    foreach($posts as $post) {
      $next->message = $post["message"];
      $next->private = $post["private"];
      $next->pictures = $this->_pictures($user_id, $post["id"]);
      $next->thumbnail = $this->_thumbnail($user_id, $post["id"]);
      array_push($result, $next);
      $next = null;
    }
    return $result;
  }

  private function _postsFriend($friend_id) {
    $result = array();
    $posts = $this->_model_post->postsBy($friend_id);
    foreach($posts as $post) {
      if(!$post["private"]) {
        $next->message = $post["message"];
        $next->pictures = $this->_pictures($friend_id, $post["id"]);
        $next->thumbnail = $this->_thumbnail($friend_id, $post["id"]);
        array_push($result, $next);
        $next = null;
      }
    }
    return $result;
  }

  private function _rc4EncryptedDataFromPayload($payload) {
    $result = null;
    $result->key = substr($payload, 0, 5);
    $result->credentials = substr($payload, 5);
    return $result;
  }

  private function _thumbnail($user_id, $post_id) {
    $result = $this->_model_picture->thumbnailBy($user_id, $post_id);
    if(isset($result) && isset($result[0]) && isset($result[0]["url"])) {
      return $result[0]["url"];
    }
    return null;
  }

  private function _updateActionsTowardsOtherUsers($user_id, $others) {
    foreach($others as $other) {
      $status = null;
      if($this->_userLegal($other)) {
        $status = $this->_model_friends->status($user_id, $other->id);
        if($status == $GLOBALS["friend_status"]["approved"] || 
            $status == $GLOBALS["friend_status"]["request_sent"]
          ) {
            $other->actions = array($GLOBALS["friend_action"]["remove"]);
        }
        else if($status == $GLOBALS["friend_status"]["pending_approval"]) {
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
        $err_msg = "One of the other users data retrieved from the database is illelgal";
        throw new Exception(err_msg);
      }
    }
  }

  private function _usersFilterFromReqURI($req_uri) {
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
    $splitted = explode("/", $req_uri);
    return intval($splitted[2]);
  }

}

?>