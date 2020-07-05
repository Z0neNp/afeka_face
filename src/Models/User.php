<?php

namespace AfekaFace\Models;

class User {

  public function details($id) {
    if($id == 2) {
      $result->first_name = "John";
      $result->last_name = "Doe";
      return $result;
    }
    return null;
  }

}