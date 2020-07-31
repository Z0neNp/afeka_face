function credentialsContainerObj() {
  let _data = undefined;
  let _id = undefined;
  return {
    set: function(data) {
      _data = data;
    },
    setId: function(id) {
      _id = id;
    },
    get: function() {
      return rc4_encrypt(JSON.stringify(_data), "abcde");
    },
    getId: function() {
      return _id;
    }
  }
}

function credentialsLegal(credentials) {
  let name_regex = new RegExp(/^[a-zA-Z]+$/g);
  let password_regex = new RegExp(/^[a-zA-Z_]+$/g);
  return credentials.first_name.match(name_regex) != undefined &&
    credentials.last_name.match(name_regex) != undefined &&
    credentials.password.match(password_regex) != undefined;
}

function gatherCredentials() {
  let result = {
    first_name: document.getElementById("first_name").value,
    last_name: document.getElementById("last_name").value,
    password: document.getElementById("password").value
  }
  if(!credentialsLegal(result)) {
    resetCredentials();
    alert("Some of your information is illegal.\nTry again.");
    return undefined;
  }
  return result;
}

function resetCredentials() {
  document.getElementById("password").value = "";
  document.getElementById("last_name").value = "";
  document.getElementById("first_name").value = "";
}

function login() {
  let payload = undefined;
  let xhr = new XMLHttpRequest();
  let credentials = gatherCredentials();
  if(credentials) {
    credentials_container.set(credentials);
    payload = credentials_container.get();
    xhr.open("POST", `/login`, true);
    xhr.setRequestHeader("Content-Type", "application/text");
    xhr.onload = function(e) {
      if(xhr.readyState === 4) {
        if(xhr.status === 200) {
          let response = JSON.parse(xhr.responseText);
          if(response["id"]) {
            credentials_container.setId(response["id"]);
            userHome(response["id"]);
          } else {
            alert("Login has failed!\n" + response["error"]);
          }
        } else {
          alert(xhr.statusText);
        }
      }
    }
    xhr.onerror = function(e) {
      alert(xhr.statusText);
    };
    xhr.send(payload); 
  }
}

function signup() {
  let payload = undefined;
  let xhr = new XMLHttpRequest();
  let credentials = gatherCredentials();
  if(credentials) {
    credentials_container.set(credentials);
    payload = credentials_container.get();
    xhr.open("POST", `/signup`, true);
    xhr.setRequestHeader("Content-Type", "application/text");
    xhr.onload = function(e) {
      if(xhr.readyState === 4) {
        if(xhr.status === 200) {
          let response = JSON.parse(xhr.responseText);
          if(response["id"]) {
            credentials_container.setId(response["id"]);
            alert("Your account has been created!\nYou will be redirected to your page");
            userHome(response["id"]);
          } else {
            alert("Account creation has failed!\n" + response["error"]);
          }
        } else {
          console.error(xhr.statusText);
        }
      }
    };
    xhr.onerror = function(e) {
      console.error(xhr.statusText);
    };
    xhr.send(payload);
  }
}

var credentials_container = credentialsContainerObj();