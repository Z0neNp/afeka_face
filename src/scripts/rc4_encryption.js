function rc4_swap(container, i, j) {
  let temp = container[i];
  container[i] = container[j];
  container[j] = temp;
}

function rc4_chr(code) {
  return String.fromCharCode(code);
}

function rc4_ord(ch) {
  return ch.charCodeAt(0);
}

function rc4_setup(key) {
  let payload = [];
  let i = 0;
  let j = 0;
  for(i = 0; i < 256; i++) {
    payload.push(i);
  }
  for(i = 0; i < 256; i++) {
    j = (j + payload[i] + rc4_ord(key[i % key.length])) % 256;
    rc4_swap(payload, i, j);
  }
  return payload;
}

function rc4_crypt(text, key) {
  let result = "";
  let i = 0;
  let j = 0;
  let payload = rc4_setup(key);
  for(let c = 0; c < text.length; c++) {
    i = (i + 1) % 256;
    j = (j + payload[i]) % 256;
    rc4_swap(payload, i, j);
    let temp = (payload[i] + payload[j]) % 256;
    result = result + rc4_chr(rc4_ord(text[c]) ^ payload[temp]);
  }
  return result;
}

function rc4_decrypt(encrypted_text, key) {
  return rc4_crypt(encrypted_text, key);
}

function rc4_encrypt(plain_text, key) {
  let result = [];
  let i = 0;
  let j = 0;
  let payload = rc4_setup(key);
  for(let c = 0; c < plain_text.length; c++) {
    i = (i + 1) % 256;
    j = (j + payload[i]) % 256;
    rc4_swap(payload, i, j);
    let temp = (payload[i] + payload[j]) % 256;
    result.push(rc4_ord(plain_text[c]) ^ payload[temp]);
  }
  return result;
}

function rc4_key(length) {
  let result = "";
  let dictionary = "abcdefghijklmnopqrstuvwxyz";
  for(let i = 0; i < length; i++) {
    result = result + dictionary.charAt(Math.floor(Math.random() * dictionary.length));
  }
  return result;
}