<?php

class AuthenticationView {

  public function userDetails() {
    $result = "<div><label for=\"first_name\">First name: </label>";
    $result = $result . "<input type=\"text\" id=\"first_name\" name=\"first_name\"><br><br>";
    $result = $result . "<label for=\"last_name\">Last name: </label>";
    $result = $result . "<input type=\"text\" id=\"last_name\" name=\"last_name\"><br><br>";
    $result = $result . "<label for=\"password\">Password: </label>";
    return $result . "<input type=\"password\" id=\"password\" name=\"password\"><br><br>";
  }

  public function containerLogin() {
    return $this->userDetails() . "<input type=\"submit\" value=\"Submit\" onclick=\"login()\"></div>";
  }
  
  public function containerSignup() {
    return $this->userDetails() . "<input type=\"submit\" value=\"Submit\" onclick=\"signup()\"></div>";
  }
}

?>