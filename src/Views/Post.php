<?php

class PostView {

  public function htmlContainer($posts) {
    $result = "";
    $count = 0;
    foreach($posts as $post) {
      if($count == 0) {
        $result = $result . "<div class=\"row\">";
      }
      $result = $result . "<div class=\"col-sm-6\">";
      $result = $result . $this->_postContainer($post);
      $result = $result . "</div>";
      $count = $count + 1;
      if($count == 2) {
        $result = $result . "</div>";
        $count = 0;
      }
    }
    if($count == 1) {
      $result = $result . "</div>";
    }
    return $result;
  }

  public function _postContainer($post) {
    $result = $result . "<div class=\"card\">";
    if(isset($post->thumbnail)) {
      $result = $result . "<img src=\"$post->thumbnail\" class=\"card-img-top\"";
      $result = $result . " style=\"max-height: 20rem;\"alt=\"Thumbnail\">";
    }
    if($post->private) {
      $result = $result . " <h5 class=\"card-title\">Private post</h5>";
    }
    else {
      $result = $result . " <h5 class=\"card-title\">Public post</h5>";
    }
    $result = $result . "<div class=\"card-body\">";
    if(isset($post->message)) {
      $result = $result . "<p class=\"card-text\">$post->message</p>";
    }
    if(isset($post->pictures)) {
      foreach($post->pictures as $picture) {
        $result = $result . "<p>$picture</p>";
      }
    }
    return $result . "</div></div>";
  }
}

?>