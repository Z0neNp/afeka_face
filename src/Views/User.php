<?php

namespace AfekaFace\Views;

class User {

  public function home($user) {
    return "<div><span>{$user->first_name}</span> <span>{$user->last_name}</span></div>";
  }
}