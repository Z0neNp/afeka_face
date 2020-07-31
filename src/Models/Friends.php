<?php

class Friends {

  private $_db;

  public function all($id) {
    $result = [];
    $relationships = $this->_allRelationships();
    foreach($relationships as $relationship) {
      if($relationship["user_id"] == $id) {
        $next = null;
        $next->id = $relationship["friend_id"];
        $next->status = $relationship["status"];
        array_push($result, $next);
      }
    }
    return $result;
  }

  public function createScheme() {
    $query = "CREATE TABLE IF NOT EXISTS friends(";
    $query = $query . "user_id INT NOT NULL,";
    $query = $query . "friend_id INT NOT NULL,";
    $query = $query . "status VARCHAR(25) NOT NULL,";
    $query = $query . "PRIMARY KEY(user_id, friend_id),";
    $query = $query . "FOREIGN KEY(user_id) REFERENCES users(id),";
    $query = $query . "FOREIGN KEY(user_id) REFERENCES users(id));";
    $this->_db->execute($query);
  }

  public function drop() {
    $query = "DROP TABLE IF EXISTS friends;";
    $this->_db->execute($query);
  }

  public function populate() {
    $users = $this->_relationships();
    foreach($users as $user) {
      $query = "SELECT id FROM users WHERE first_name = \"$user->first_name\"";
      $query = $query . " AND last_name = \"$user->last_name\";";
      $user_id = $this->_db->query($query)[0]["id"];
      foreach($user->friends as $friend) {
        $query = "SELECT id FROM users WHERE first_name = \"$friend->first_name\"";
        $query = $query . " AND last_name = \"$friend->last_name\";";
        $friend_id = $this->_db->query($query)[0]["id"];
        $this->_db->execute(
          "INSERT INTO friends VALUES($user_id, $friend_id, \"$friend->status\");"
        );
      }
    }
  }

  public function setDb($db) {
    $this->_db = $db;
  }

  public function status($user_id, $friend_id) {
    $result = null;
    $relationships = $this->_allRelationships();
    foreach($relationships as $relationship) {
      if($relationship["user_id"] == $user_id) {
        if($relationship["friend_id"] == $friend_id) {
          $result = $relationship["status"];
          break;
        }
      }
    }
    if(!isset($result)) {
      return $GLOBALS["friend_status"]["unacquainted"];
    }
    return $result;
  }

  public function statusToApproved($user_id, $friend_id) {
    $this->_updateStatus($user_id, $friend_id, $GLOBALS["friend_status"]["approved"]);
    $this->_updateStatus($friend_id, $user_id, $GLOBALS["friend_status"]["approved"]);
  }

  public function statusToUnacquainted($user_id, $friend_id) {
    $this->_removeRelationship($user_id, $friend_id);
    $this->_removeRelationship($friend_id, $user_id);
  }

  public function statusToRequestSent($user_id, $friend_id) {
    $this->_addRelationship($user_id, $friend_id, $GLOBALS["friend_status"]["request_sent"]);
    $this->_addRelationship($friend_id, $user_id, $GLOBALS["friend_status"]["pending_approval"]);
  }

  private function _addRelationship($user_id, $friend_id, $status) {
    $query = "INSERT INTO friends VALUES ($user_id, $friend_id, \"$status\");";
    $this->_db->execute($query);
  }

  private function _allRelationships() {
    return $this->_db->query("SELECT * FROM friends;");
  }

  private function _relationships() {
    $users = array();
    $user->first_name = "John";
    $user->last_name = "Doe";
    $user->friends = array();
    $friend->first_name = "Jane";
    $friend->last_name = "Doe";
    $friend->status = $GLOBALS["friend_status"]["request_sent"];
    array_push($user->friends, $friend);
    array_push($users, $user);

    $user = null;
    $user->first_name = "Jane";
    $user->last_name = "Doe";
    $user->friends = array();
    $friend = null;
    $friend->first_name = "John";
    $friend->last_name = "Doe";
    $friend->status = $GLOBALS["friend_status"]["pending_approval"];
    array_push($user->friends, $friend);
    $friend = null;
    $friend->first_name = "Jack";
    $friend->last_name = "Hall";
    $friend->status = "request sent";
    array_push($user->friends, $friend);
    $friend = null;
    $friend->first_name = "Kevin";
    $friend->last_name = "Smith";
    $friend->status = "approved";
    array_push($user->friends, $friend);
    array_push($users, $user);

    $user = null;
    $user->first_name = "Jack";
    $user->last_name = "Hall";
    $user->friends = array();
    $friend = null;
    $friend->first_name = "Jane";
    $friend->last_name = "Doe";
    $friend->status = $GLOBALS["friend_status"]["pending_approval"];
    array_push($user->friends, $friend);
    array_push($users, $user);

    $user = null;
    $user->first_name = "Kevin";
    $user->last_name = "Smith";
    $user->friends = array();
    $friend = null;
    $friend->first_name = "Jane";
    $friend->last_name = "Doe";
    $friend->status = $GLOBALS["friend_status"]["approved"];
    array_push($user->friends, $friend);
    array_push($users, $user);

    return $users;
  }

  private function _removeRelationship($user_id, $friend_id) {
    $query = "DELETE FROM friends WHERE";
    $query = $query . " user_id = $user_id AND friend_id = $friend_id";
    $this->_db->execute($query);
  }

  private function _updateStatus($user_id, $friend_id, $status) {
    $query = "UPDATE friends SET status = \"$status\"";
    $query = $query . " WHERE user_id = $user_id AND friend_id = $friend_id;";
    $this->_db->execute($query);
  }

}

?>