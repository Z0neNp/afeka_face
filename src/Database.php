<?php

class Database {

  private $_conn;

  public function closeConnection() {
    if($this->_conn->close()) {
      $this->_conn = null;
      return;
    }
    throw new Exception("Connection to the database has not been terminated as expected");
  }

  public function execute($query) {
    $stmt = $this->_conn->prepare($query);
    if($stmt == FALSE) {
      throw new Exception("Failed to prepare the SQL statement\n$query");
    }
    $result = $stmt->execute();
    if(!$result) {
      throw new Exception("Failed to executed a prepared statement\n$query");
    }
    $result = $stmt->close();
    if(!$result) {
      throw new Exception("Failed to close an executed statement\n$query");
    }
  }

  public function initConnection() {
    if(!isset($this->_conn)) {
      $this->_conn = new mysqli("localhost", "root", "root", "afeka_face");
      if($this->_conn->connect_errno) {
        throw new Exception("Connection to the database has failed." . $mysqli->connect_error());
      }
    }
  }

  public function lastInsertId() {
    $result = $this->query("SELECT LAST_INSERT_ID();");
    print_r("LAST INSERT ID IS");
    print_r("<br><br>");
    print_r($result);
    print_r("<br><br>");
    return $result[0]["LAST_INSERT_ID()"];
  }

  public function query($query) {
    $result = array();
    $response = $this->_conn->query($query);
    if($response == FALSE) {
      throw new Exception("Failed to query the database with\n$query");
    }
    while($row = $response->fetch_assoc()) {
      array_push($result, $row);
    }
    $response->free();
    $response->close();
    return $result;
  }
}

?>