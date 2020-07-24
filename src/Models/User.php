<?php

class User {

  private $_db;
  private $_users;

  public function createScheme() {
    $query = "CREATE TABLE IF NOT EXISTS users(";
    $query = $query . "id INT AUTO_INCREMENT,";
    $query = $query . "first_name VARCHAR(25) NOT NULL,";
    $query = $query . "last_name VARCHAR(25) NOT NULL,";
    $query = $query . "password VARCHAR(500) NOT NULL,";
    $query = $query . "PRIMARY KEY(id));";
    $this->_db->execute($query); 
  }
  
  public function details($id) {
    $result = null;
    $user = $this->_userById($id);
    if(isset($user["id"]) && isset($user["first_name"]) && isset($user["last_name"])) {
      $result->id = $user["id"];
      $result->first_name = $user["first_name"];
      $result->last_name = $user["last_name"];
    }
    return $result;
  }

  public function drop() {
    $query = "DROP TABLE IF EXISTS users;";
    $this->_db->execute($query);
  }

  public function isPersisted($first_name, $last_name) {
    $user_id = $this->_id($first_name, $last_name);
    return isset($user_id);
  }

  public function new($first_name, $last_name, $password) {
    $query = "INSERT INTO users(first_name, last_name, password) VALUES";
    $query = $query . "(\"$first_name\", \"$last_name\", \"$password\");";
    $this->_db->execute($query);
    return $this->_id($first_name, $last_name);
  }

  public function others($id, $filter) {
    $filter = preg_replace('/\s+/', '', $filter);
    $filter = strtolower($filter);
    $result = [];
    $users = $this->_all();
    foreach($users as $user) {
      if($user["id"] != $id) {
        $name = strtolower($user["first_name"]) . strtolower($user["last_name"]);
        if(preg_match("#^.*{$filter}.*$#", $name) || $filter == "") {
          $next->id = $user["id"];
          $next->first_name = $user["first_name"];
          $next->last_name = $user["last_name"];
          array_push($result, $next);
          $next = null;
        }
      }
    }
    return $result;
  }

  public function setDb($db) {
    $this->_db = $db;
  }

  public function populate() {
    $query = $query = "INSERT INTO users(first_name, last_name, password) VALUES";
    $query = $query . "(\"John\", \"Doe\", \"49,216,39,71,195,186,6,74,167,78,106,44,187,2,115,144,153,171,126,199,167,172,165,208,224,227,71,45,71,211,128,99,37,14,164,191,250,107,18,217,228,203,201,236,124,236,95,117,147,91,176,62,111,169,246,41,205,108,39,137,199\"),";
    $query = $query . "(\"Jane\", \"Doe\", \"49,216,39,71,195,186,6,74,167,78,106,44,187,2,115,144,151,173,117,199,167,172,165,208,224,227,71,45,71,211,128,99,37,14,164,191,250,107,18,217,228,203,201,236,124,236,95,117,147,91,176,62,97,175,253,41,205,108,39,137,199\"),";
    $query = $query . "(\"Jack\", \"Hall\", \"49,216,39,71,195,186,6,74,167,78,106,44,187,2,115,144,151,160,123,199,167,172,165,208,224,227,71,45,71,211,128,99,37,14,168,177,243,37,28,215,182,218,219,236,120,244,66,99,213,67,168,118,106,160,251,29,246,107,35,199,214,87,124\"),";
    $query = $query . "(\"Kevin\", \"Smith\", \"49,216,39,71,195,186,6,74,167,78,106,44,187,2,115,145,147,181,121,139,169,162,235,221,242,228,108,28,72,223,136,36,61,22,194,131,242,32,74,147,182,134,152,239,106,240,94,102,222,19,246,118,58,227,243,19,223,106,44,244,201,24,104,127,18,27,93\"),";
    $query = $query . "(\"Brook\", \"McConoughy\", \"49,216,39,71,195,186,6,74,167,78,106,44,187,2,115,152,132,172,127,142,169,162,235,221,242,228,108,28,72,223,136,36,61,22,194,157,252,10,81,149,251,223,221,247,114,161,1,51,193,0,225,39,119,174,234,18,139,57,96,201,200,26,110,96,37,84,67,67,62,223,171,169,153,228,241,151,22\");";
    $this->_db->execute($query);
  }

  public function userId($first_name, $last_name, $password) {
    $result = null;
    $user = $this->_db->query(
      "SELECT id, password FROM users WHERE first_name = \"$first_name\" and last_name = \"$last_name\";"
    )[0];
    if($user["password"] == $password) {
      $result = $user["id"];
    }
    return $result;
  }

  private function _all() {
    return $this->_db->query(
      "SELECT * FROM users;"
    );
  }

  private function _id($first_name, $last_name) {
    return $this->_db->query(
      "SELECT id FROM users WHERE first_name = \"$first_name\" and last_name = \"$last_name\";"
    )[0]["id"];
  }

  private function _userById($id) {
    return $this->_db->query("SELECT * FROM users WHERE id = $id;")[0];
  }

}

?>