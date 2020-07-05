<?php

namespace AfekaFace\Views;

class User {

  public function home($user, $friends) {
    $result = "<div><span>{$user->first_name}</span> <span>{$user->last_name}</span>";
    $result = "{$result}<table><thead><tr>";
    $result = "{$result}<th>First name</th><th>Last Name</th><th>Status</th></tr></thead>";
    $result = "{$result}<tbody>";
    foreach($friends as $friend) {
      $result = "{$result}<tr><td>{$friend->first_name}</td><td>{$friend->last_name}</td><td>{$friend->status}</td>";
    }
    $result = "{$result}</tbody></table>";
    return "{$result}</div>";
  }
}