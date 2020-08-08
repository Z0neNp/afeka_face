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

  public function htmlContainerNewForm($user_id) {
    $result = "<div><button style=\"width:100%;\" class=\"btn btn-info\"";
    $result = $result . " onclick=\"userHome($user_id);\">Back</button></div>";
    $result = $result . "<form>";

    $result = $result . "<div class=\"form-group\"><label for=\"post_message\">";
    $result = $result . "Text message</label>";
    $result = $result . "<textarea id=\"post_message\" rows=\"4\" cols=\"50\"";
    $result = $result . " class=\"form-control\" name=\"post_message\"></textarea>";
    $result = $result . "<small id=\"post_message_help\" class=\"form-text text-muted\">";
    $result = $result . "I.e. Hello, world!</small></div>";

    $result = $result . "<div class=\"form-group\"><label for=\"post_thumbnail\">";
    $result = $result . "Thumbnail</label>";
    $result = $result . "<input type=\"text\" id=\"post_thumbnail\" class=\"form-control\"";
    $result = $result . " name=\"post_thumbnail\">";
    $result = $result . "<small id=\"post_thumbnail_help\" class=\"form-text text-muted\">";
    $result = $result . "I.e. https://images.unsplash.com/photo-1537182534312-f945134cce34?ixlib=rb";
    $result = $result . "-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1050&q=80";
    $result = $result . "</small></div>";

    for($counter = 0; $counter < 6; $counter++) {
      $result = $result . "<div class=\"form-group\"><label for=\"post_image_$counter\">";
      $result = $result . "Image #$counter</label>";
      $result = $result . "<input type=\"text\" id=\"post_image_$counter\"";
      $result = $result . " class=\"form-control\" name=\"post_image_$counter\">";
      $result = $result . "<small id=\"post_image_{$counter}_help\" class=\"form-text";
      $result = $result . " text-muted\">I.e. https://images.unsplash.com/photo-1537182534312";
      $result = $result . "-f945134cce34?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&";
      $result = $result . "fit=crop&w=1050&q=80</small></div>";
    }

    $result = $result . "<div class=\"form-group form-check\">";
    $result = $result . "<input type=\"checkbox\" class=\"form-check-input\"";
    $result = $result . " id=\"post_private\"><label class=\"form-check-label\"";
    $result = $result . " for=\"post_private\">Private post</label></div></form>";

    $result = $result . "<button class=\"btn btn-primary\" style=\"width:100%;\"";
    return $result . " onclick=\"submitNewPostData()\">Submit</button>";
  }

  private function _picturesContainer($pictures) {
    $result = "";
    if(isset($pictures)) {
      $counter = 0;
      $result = $result . "<div class=\"d-flex flex-row bd-highlight mb-3\">";
      for(; $counter < count($pictures) / 2; $counter++) {
        $result = $result . "<div class=\"p-2\">";
        $result = $result . "<img src=\"$pictures[$counter]\" style=\"max-height: 10rem;\"";
        $result = $result . "id=\"carousel_post_image_$counter\" alt=\"Post image #$counter\">";
        $result = $result . "</div>";
      }
      $result = $result . "</div>";
      $result = $result . "<div class=\"d-flex flex-row bd-highlight mb-3\">";
      for(; $counter < count($pictures); $counter++) {
        $result = $result . "<div class=\"p-2\">";
        $result = $result . "<img src=\"$pictures[$counter]\" style=\"max-height: 10rem;\"";
        $result = $result . "id=\"carousel_post_image_$counter\" alt=\"Post image #$counter\">";
        $result = $result . "</div>";
      }
      $result = $result . "</div>";
    }
    return $result;
  }

  private function _postContainer($post) {
    $result = $result . "<div class=\"card\">";
    if(isset($post->thumbnail)) {
      $result = $result . "<img src=\"$post->thumbnail\" class=\"card-img-top\"";
      $result = $result . " style=\"max-height: 15rem;\"alt=\"Thumbnail\">";
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
    $result = $result . $this->_picturesContainer($post->pictures);
    return $result . "</div></div>";
  }
}

?>