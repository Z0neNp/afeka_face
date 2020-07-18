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

  public function encrypt($plain_text, $key) {
    $this->_key = $key;
    $this->_payload = array();
    return $this->_crypt($plain_text);
  }

  private function _setup($key) {
    $key_length = strlen($key);
    $payload = array();
    $i = 0;
    $j = 0;
    for($i = 0; $i < 256; $i++) {
      array_push($payload, $i);
    }
    for($i = 0; $i < 256; $i++) {
      $j = ($j + $payload[$i] + ord($key[$i % $key_length])) % 256;
      $temp = $payload[$i];
      $payload[$i] = $payload[$j];
      $payload[$j] = $temp;
    }
    return $payload;
  }

  private function _crypt($text) {
    $result = "";
    $length = strlen($text);
    $this->_setup();
    $i = 0;
    $j = 0;
    for($c = 0; $c < $length; $c++) {
      $i = ($i + 1) % 256;
      $j = ($j + $this->_payload[$i]) % 256;
      $this->_swap($i, $j);
      $temp = ($this->_payload[$i] + $this->_payload[$j]) % 256;
      $result = $result . chr($text[$c] ^ $this->_payload[$temp]);
    }
    return $result;
  }

  private function _swap($i, $j) {
    $temp = $this->_payload[$i];
    $this->_payload[$i] = $this->_payload[$j];
    $this->_payload[$j] = $temp;
  }
}