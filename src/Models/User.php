<?php

namespace AfekaFace\Models;

class User {

  private $_db;

  public function details($id) {
    $result = null;
    if($id == 1) {
      $result->first_name = "Jane";
      $result->last_name = "Doe";
      return $result;
    }
    else if($id == 2) {
      $result->first_name = "John";
      $result->last_name = "Doe";
      return $result;
    }
    if($id == 3) {
      $result->first_name = "Jack";
      $result->last_name = "Hall";
      return $result;
    }
    if($id == 4) {
      $result->first_name = "Kevin";
      $result->last_name = "Smith";
      return $result;
    }
    return null;
  }

}