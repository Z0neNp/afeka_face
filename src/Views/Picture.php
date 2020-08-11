<?php

class PictureView {

  public function htmlContainer($user_id, $url) {
    $result = "<div><button style=\"width:100%;\" class=\"btn btn-info\"";
    $result = $result . " onclick=\"userHome($user_id);\">Back</button></div>";
    $result = $result . "<div class=\"container\"><img src=\"$url\" alt=\"Enlarged image\"></div>";
    return $result;
  }
}

?>