<?php

class Home {

  private $_view;

  public function htmlContainer() {
    return $this->_view->view();
  }

  public function setView($view) {
    $this->_view = $view;
  }
}