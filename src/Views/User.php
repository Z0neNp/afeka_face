<?php

class UserView {

  public function view($user, $friends) {
    $friendsContainer = $this->_friendsContainer($user, $friends);
    $result = $result . "<div class=\"alert alert-primary\">Posts</div><hr>";
    $result = $result . "<div class=\"alert alert-primary\">Friends list</div><hr>" . $friendsContainer;
    $result = $result . "<div class=\"alert alert-primary\">Friends management</div><hr>";
    return $result;
  }

  public function viewFriend($user_id, $friend) {
    $result = "<div><button style=\"width:100%;\" class=\"btn btn-primary\" onclick=\"userHome($user_id);\">";
    $result = $result. "Back</button></div><hr>";
    $result = $result . "<div class=\"alert alert-primary\">{$friend->first_name} ";
    $result = $result . "{$friend->last_name} Posts</div><hr>";
    return $result;
  }

  public function othersList($user_id, $others) {
    $result = "<form>";
    $result = "<div class=\"form-group\">";
    $result = $result . "<input type=\"text\" id=\"filter_users\" class=\"form-control\"";
    $result = $result . "value=\"*\"";
    $result = $result . "<small id=\"last_name_help\" class=\"form-text text-muted\">";
    $result = $result . "I.e. Jane</small>";
    $result = $result . "<table class=\"table\"><tbody>";
    foreach($others as $other) {
      $result = $result . "<tr>";
      $result = $result . "<td>" . $other->first_name . " " . $other->last_name . "</td>";
      foreach($other->actions as $action) {
        $result = $result . "<td><button class=\"btn btn-primary\" onclick=\"updateFriendStatus(";
        $result = $result . "$user_id, $other->id, '$action');\">$action</button></td>";
      }
      $result = $result . "</tr>";
    }
    return $result . "</table></form>";
  }

  private function _friendsContainer($user, $friends) {
    $result = $result . "<table class=\"table\"><thead><tr>";
    $result = $result . "<th scope=\"col\">First name</th>";
    $result = $result . "<th scope=\"col\">Last Name</th>";
    $result = $result . "<th scope=\"col\">Status</th>";
    $result = $result . "</tr></thead><tbody>";
    foreach($friends as $friend) {
      $result = $result . "<tr onclick=\"userFriend($user->id, $friend->id)\"><td>";
      $result = $result . "{$friend->first_name}</td><td>";
      $result = $result . "{$friend->last_name}</td><td>{$friend->status}</td></tr>";
    }
    return $result . "</tbody></table>";
  }
}

?>