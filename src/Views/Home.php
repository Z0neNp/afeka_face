<?php

namespace AfekaFace\Views;

class Home {

  public function view() {
    $result = "<div>Welcome to the Afeka Face!</div>";
    $result = $result . "<div><a href=\"login\">Login</a></div>";
    $result = $result . "<div><a href=\"signup\">Signup</a></div>";
    return $result;
  }
}