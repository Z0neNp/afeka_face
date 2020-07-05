<?php

namespace AfekaFace\Models;

class Friends {

  private $_db;

  public function all($id) {
    $friends = [];
    if($id == 1) {
      $friend->id = 2;
      $friend->status = "request sent";
      array_push($friends, $friend);
    }
    else if($id == 2) {
      $a->id = 1;
      $a->status = "pending approval";
      array_push($friends, $a);
      $b->id = 3;
      $b->status = "request sent";
      array_push($friends, $b);
      $c->id = 4;
      $c->status = "approved";
      array_push($friends, $c);
    }
    else if($id == 3) {
      $friend->id = 2;
      $friend->status = "pending approval";
      array_push($friends, $friend);
    }
    else if($id == 4) {
      $friend->id = 2;
      $friend->status = "approved";
      array_push($friends, $friend);
    }
    return $friends;
  }

}