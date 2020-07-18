function credentialsContainerObj() {
  let _data = undefined;
  return {
    set: function(data) {
      _data = data;
    },
    get: function() {
      return rc4_encrypt(JSON.stringify(_data), "abcde");
    }
  }
}

function gatherCredentials() {
  let result = {
    first_name: document.getElementById("first_name").value,
    last_name: document.getElementById("last_name").value,
    password: document.getElementById("password").value
  }
  return result;
}

function login() {
  let credentials = gatherCredentials();
  console.log(credentials);
}

function singup() {
  let payload = undefined;
  let xhr = new XMLHttpRequest();
  credentials_container.set(gatherCredentials());
  payload = credentials_container.get();
  xhr.open("POST", `${window.location.href}`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        let response = JSON.parse(xhr.responseText);
        if(response["id"]) {
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

var credentials_container = credentialsContainerObj();