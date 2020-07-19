<?php

class Database {

  private $_conn;

  public function closeConnection() {
    $this->_conn->close();
  }

  public function execute($query) {
    $stmt = $this->_conn->prepare($query);
    $stmt->execute();
    $stmt->close();
  }

  public function initConnection() {
    if(isset($this->_conn)) {
      return;
    }
    $this->_conn = new mysqli("localhost", "root", "root", "afeka_face");
    if($this->_conn->connect_errno) {
      echo "Connect failed" . $mysqli->connect_error;
      exit(1);
    }
  }

  public function query($query) {
    $result = array();
    $response = $this->_conn->query($query);
    while($row = $response->fetch_assoc()) {
      array_push($result, $row);
    }
    $response->free();
    $response->close();
    return $result;
  }

  private function _dropFriends() {
    $query = "DROP TABLE IF EXISTS friends";
    $this->_executeQuery($query);
  }

  private function _createFriends() {
    
  }
}

?>