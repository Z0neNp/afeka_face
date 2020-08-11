<?php

class Picture {
  private $_db;

  public function createScheme() {
    $query = "CREATE TABLE IF NOT EXISTS picture(";
    $query = $query . "id INT AUTO_INCREMENT,";
    $query = $query . "user_id INT NOT NULL,";
    $query = $query . "post_id INT NOT NULL,";
    $query = $query . "url VARCHAR(300) NOT NULL,";
    $query = $query . "thumbnail BOOLEAN DEFAULT FALSE,";
    $query = $query . "PRIMARY KEY(id),";
    $query = $query . "FOREIGN KEY(user_id) REFERENCES user(id),";
    $query = $query . "FOREIGN KEY(post_id) REFERENCES post(id));";
    $this->_db->execute($query);
  }

  public function drop() {
    $query = "DROP TABLE IF EXISTS picture;";
    $this->_db->execute($query);
  }

  public function new($user_id, $post_id, $url, $thumbnail) {
    $query = "INSERT INTO picture(user_id,post_id,url,thumbnail)";
    $query = $query . " VALUES($user_id,$post_id,\"$url\",$thumbnail);";
    $this->_db->execute($query);
  }

  public function populate() {
    $posts = $this->_db->query("SELECT * FROM post;");
    foreach($posts as $post) {
      $user_id = $post["user_id"];
      $post_id = $post["id"];
      if($post["private"]) {
        $query = "INSERT INTO picture(user_id,post_id,url,thumbnail) VALUES($user_id,$post_id,";
        $query = $query . "\"https://images.unsplash.com/photo-1537182534312-f945134cce34?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1050&q=80\",";
        $query = $query . "TRUE);";
      }
      else {
        $query = "INSERT INTO picture(user_id,post_id,url,thumbnail) VALUES($user_id,$post_id,";
        $query = $query . "\"https://images.unsplash.com/photo-1532173311168-91e999ce4e47?ixlib=";
        $query = $query . "rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=701&q=80\",";
        $query = $query . "TRUE);";
      }
      $this->_db->execute($query);
    }
  }

  public function picturesBy($user_id, $post_id) {
    $query = "SELECT * FROM picture WHERE user_id = $user_id AND post_id = $post_id;";
    return $this->_db->query($query);
  }

  public function setDb($db) {
    $this->_db = $db;
  }

  public function thumbnailBy($user_id, $post_id) {
    $query = "SELECT * FROM picture WHERE user_id = $user_id AND post_id = $post_id";
    $query = $query . " AND thumbnail = TRUE;";
    return $this->_db->query($query);
  }

  public function urlBy($picture_id) {
    $query = "SELECT * FROM picture WHERE id = $picture_id;";
    return $this->_db->query($query)[0]["url"];
  }
}

?>