<?php

namespace AfekaFace\Models;

class User {

  private $_db;
  private $_users;

  public function details($id) {
    $this->_setUsers();
    $result = null;
    foreach($this->_users as $user) {
     if($user["id"] == $id) {
      $result->id = $id;
      $result->first_name = $user["first_name"];
      $result->last_name = $user["last_name"];
      break;
     } 
    }
    return $result;
  }

  public function others($id, $filter) {
    $this->_setUsers();
    $filter = preg_replace('/\s+/', '', $filter);
    $filter = strtolower($filter);
    $result = [];
    foreach($this->_users as $user) {
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

  private function _setUsers() {
    // TODO: remove when there is a connection to the db
    $result = [];
    array_push($result, array("id" => 1, "first_name" => "Jane", "last_name" => "Doe"));
    array_push($result, array("id" => 2, "first_name" => "John", "last_name" => "Doe"));
    array_push($result, array("id" => 3, "first_name" => "Jack", "last_name" => "Hall"));
    array_push($result, array("id" => 4, "first_name" => "Kevin", "last_name" => "Smith"));
    $this->_users = $result;
  }

}