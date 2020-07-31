<?php

class AuthenticationView {

  public function userDetails() {
    $result = "<form>";
    
    $result = $result . "<div class=\"form-group\"><label for=\"first_name\">First name</label>";
    $result = $result . "<input type=\"text\" id=\"first_name\" class=\"form-control\"";
    $result = $result . " name=\"first_name\">";
    $result = $result . "<small id=\"first_name_help\" class=\"form-text text-muted\">";
    $result = $result . "I.e. John</small></div>";

    $result = $result . "<div class=\"form-group\"><label for=\"last_name\">Last name</label>";
    $result = $result . "<input type=\"text\" id=\"last_name\" class=\"form-control\"";
    $result = $result . " name=\"last_name\">";
    $result = $result . "<small id=\"last_name_help\" class=\"form-text text-muted\">";
    $result = $result . "I.e. Doe</small></div>";

    $result = $result . "<div class=\"form-group\"><label for=\"password\">Password</label>";
    $result = $result . "<input type=\"password\" id=\"password\" class=\"form-control\"";
    return $result . " name=\"password\"></div></form>";
  }

  public function containerLogin() {
    $user_details = $this->userDetails();
    $result = $user_details . "<button class=\"btn btn-primary\" style=\"width:100%;\"";
    return $result . " onclick=\"login()\">Submit</button>";
  }
  
  public function containerSignup() {
    $user_details = $this->userDetails();
    $result = $user_details . "<button class=\"btn btn-primary\" style=\"width:100%;\"";
    return $result . " onclick=\"signup()\">Submit</button></div>";
  }
}

?>