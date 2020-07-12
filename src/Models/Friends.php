<?php

namespace AfekaFace\Models;

class Friends {

  private $_db;
  private $_relationships;

  public function all($id) {
    $this->_setRelationships();
    $result = [];
    foreach($this->_relationships as $relationships) {
      if($relationships["id"] == $id) {
        foreach($relationships["friends"] as $relationship) {
          $next = null;
          $next->id = $relationship["id"];
          $next->status = $relationship["status"];
          array_push($result, $next);
        }
        break;
      }
    }
    return $result;
  }

  public function status($user_id, $friend_id) {
    $this->_setRelationships();
    $result = null;
    foreach($this->_relationships as $relationships) {
      if($relationships["id"] == $user_id) {
        foreach($relationships["friends"] as $relationship) {
          if($relationship["id"] == $friend_id) {
            $result = $relationship["status"];
            break;
          }
        }
        if(isset($result)) {
          break;
        }
      }
    }
    if(!isset($result)) {
      return "unacquainted";
    }
    return $result;
  }

  private function _setRelationships() {
    // TODO: remove when db connection is active
    $result = [];
    array_push($result, array("id" => 1, "friends" => [
      array("id" => 2, "status" => "request sent")
    ]));
    array_push($result, array("id" => 2, "friends" => [
      array("id" => 1, "status" => "pending approval"),
      array("id" => 3, "status" => "request sent"),
      array("id" => 4, "status" => "approved")
    ]));
    array_push($result, array("id" => 3, "friends" => [
      array("id" => 2, "status" => "approved")
    ]));
    $this->_relationships = $result;
  }

}