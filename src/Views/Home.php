<?php

class HomeView {

  public function view() {
    $result = $result . "<div class=\"jumbotron\">";
    $result = $result . "<h1 class=\"display-4 d-flex justify-content-center\">";
    $result = $result . "Welcome to Afeka Face!</h1>";
    $result = $result . "<p class=\"lead d-flex justify-content-center\">";
    $result = $result . "<button type=\"button\" class=\"btn";
    $result = $result . " btn-primary\" onclick=\"userLogin();\">Login</button>";
    $result = $result . " | <button type=\"button\" class=\"btn btn-primary\"";
    $result = $result . " onclick=\"userSignup();\">Signup</button></p>";
    $result = $result . "<hr class=\"my-4\">";
    $result = $result . "</div>";
    return $result;
  }
}

?>