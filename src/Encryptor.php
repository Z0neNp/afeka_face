<?php

namespace AfekaFace;

// Special thanks goes to: https://pear.php.net/package/Crypt_RC4/docs/latest/__filesource/fsource_Crypt__Crypt_RC4-1.0.3CryptRc4.php.html
class Encryptor {

  private $_payload;
  private $_key;

  public function decrypt($encrypted_text, $key) {
    $key = "abcde";
    $result = "";
    $encrypted_text = explode(",", $encrypted_text);
    $length = count($encrypted_text);
    $payload = $this->_setup($key);
    $i = 0;
    $j = 0;
    for($c = 0; $c < $length; $c++) {
      $i = ($i + 1) % 256;
      $j = ($j + $payload[$i]) % 256;
      $temp = $payload[$i];
      $payload[$i] = $payload[$j];
      $payload[$j] = $temp;
      $temp = ($payload[$i] + $payload[$j]) % 256;
      $result = $result . chr($encrypted_text[$c] ^ $payload[$temp]);
    }
    return $result;
  }

  private function _setup($key) {
    $result = array();
    $key_length = strlen($key);
    $i = 0;
    $j = 0;
    for($i = 0; $i < 256; $i++) {
      array_push($result, $i);
    }
    for($i = 0; $i < 256; $i++) {
      $j = ($j + $result[$i] + ord($key[$i % $key_length])) % 256;
      $temp = $result[$i];
      $result[$i] = $result[$j];
      $result[$j] = $temp;
    }
    return $result;
  }
}

?>