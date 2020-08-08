<?php

class Post {
  private $_db;

  public function createScheme() {
    $query = "CREATE TABLE IF NOT EXISTS post(";
    $query = $query . "id INT AUTO_INCREMENT,";
    $query = $query . "user_id INT NOT NULL,";
    $query = $query . "private BOOLEAN DEFAULT FALSE,";
    $query = $query . "message VARCHAR(150),";
    $query = $query . "PRIMARY KEY(id),";
    $query = $query . "FOREIGN KEY(user_id) REFERENCES user(id));";
    $this->_db->execute($query);
  }

  public function drop() {
    $query = "DROP TABLE IF EXISTS post;";
    $this->_db->execute($query);
  }

  public function new($user_id, $private, $message) {
    if($private) {
      $private = "TRUE";
    }
    else {
      $private = "FALSE";
    }
    $query = "INSERT INTO post(user_id,private,message) VALUES($user_id,$private,\"$message\");";
    $this->_db->execute($query);
    $result = $this->_db->lastInsertId();
    return $result;
  }

  public function populate() {
    $users = $this->_db->query("SELECT * FROM user;");
    foreach($users as $user) {
      $user_id = $user["id"];
      $user_name = $user["first_name"];
      $query = "INSERT INTO post(user_id,private,message) VALUES($user_id,FALSE,";
      $query = $query . "\"Hello world, I am $user_name!\");";
      $this->_db->execute($query);
      $query = "INSERT INTO post(user_id,private,message) VALUES($user_id,TRUE,";
      $query = $query . "\"Hello me, I am $user_name!\");";
      $this->_db->execute($query);
    }
  }

  public function postsBy($user_id) {
    $query = "SELECT * FROM post WHERE user_id = $user_id;";
    return $this->_db->query($query);
  }

  public function setDb($db) {
    $this->_db = $db;
  }
}

?>