<?php

namespace AfekaFace\Views;

class User {

  public function view($user, $friends) {
    $result = "<div><span>{$user->first_name}</span> <span>{$user->last_name}</span>";
    $result = "{$result}<table><thead><tr>";
    $result = "{$result}<th>First name</th><th>Last Name</th><th>Status</th></tr></thead>";
    $result = "{$result}<tbody>";
    foreach($friends as $friend) {
      $result = "{$result}<tr onclick=\"userFriend($user->id, $friend->id)\"><td>";
      $result = $result . "{$friend->first_name}</td><td>";
      $result = $result . "{$friend->last_name}</td><td>{$friend->status}</td></tr>";
    }
    $result = "{$result}</tbody></table>";
    return "{$result}</div>";
  }

  public function viewFriend($friend) {
    return "<div><span>{$friend->first_name}</span> <span>{$friend->last_name}</span>";
  }

  public function othersList($user_id, $others) {
    $result = "<label for=\"filter_users\">Search users: </label>";
    $result = $result . "<input type=\"text\" id=\"filter_users\" value=\"*\"><br><ul>";
    foreach($others as $other) {
      $result = $result . "<li>";
      $result = $result . "{$other->first_name} {$other->last_name}";
      foreach($other->actions as $action) {
        $result = $result . " | <a href=\"/users/{$user_id}/friends/{$action}/{$other->id}\">";
        $result = $result . " {$action}</a>  ";
      }
      $result = $result . "</li>";
    }
    return $result . "</ul>";
  }
}