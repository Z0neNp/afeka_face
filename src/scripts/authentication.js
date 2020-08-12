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
      try {
        return rc4_encrypt(JSON.stringify(_data), "abcde");
      } catch(err) {
        let err_msg = "credentialsContainerObj() - failed to encrypt the credentials data\n";
        err_msg += err.message;
        throw new Exception(err_msg);
      }
    },
    getId: function() {
      return _id;
    }
  }
}

function credentialsLegal(credentials) {
  let name_regex = new RegExp(/^[a-zA-Z]+$/g);
  let password_regex = new RegExp(/^[a-zA-Z_]+$/g);
  try {
    return credentials.first_name.match(name_regex) != undefined &&
      credentials.last_name.match(name_regex) != undefined &&
      credentials.password.match(password_regex) != undefined;
  } catch(err) {
    let err_msg = "credentialsLegal() - failed to make the validation\n";
    err_msg += err.message;
    throw new Exception(err_msg);
  }
}

function gatherCredentials() {
  try {
    return {
      first_name: gatherFirstName(),
      last_name: gatherLastName(),
      password: gatherPassword()
    }
  } catch(err) {
    let err_msg = "gatherCredentials() - failed\n" + err.message;
    throw new Exception(err_msg);
  }
}

function gatherFirstName() {
  try {
    return document.getElementById("first_name").value;
  } catch(err) {
    let err_msg = "gatherFirstName() - failed to access the value from the ";
    err_msg += "first_name input element\n" + err.message;
    throw new Exception(err_msg);
  }
}

function gatherLastName() {
  try {
    return document.getElementById("last_name").value;
  } catch(err) {
    let err_msg = "gatherLastName() - failed to access the value from the ";
    err_msg += "last_name input element\n" + err.message;
    throw new Exception(err_msg);
  }
}

function gatherPassword() {
  try {
    return document.getElementById("password").value;
  } catch(err) {
    let err_msg = "gatherPassword() - failed to access the value from the ";
    err_msg += "password input element\n" + err.message;
    throw new Exception(err_msg);
  }
}

function resetCredentials() {
  try {
    resetFirstName();
    resetLastName();
    resetPassword();
  } catch(err) {
    let err_msg = "resetCredentials() - failed\n" + err.message;
    throw new Exception(err_msg);
  }
}

function resetFirstName() {
  try {
    document.getElementById("first_name").value = "";
  } catch(err) {
    let err_msg = "resetFirstName() - failed to access the value in the ";
    err_msg = "first_name input element\n" + err.message;
    throw new Exception(err_msg);
  }
}

function resetLastName() {
  try {
    document.getElementById("last_name").value = "";
  } catch(err) {
    let err_msg = "resetLastName() - failed to access the value in the ";
    err_msg = "last_name input element\n" + err.message;
    throw new Exception(err_msg);
  }
}

function resetPassword() {
  try {
    document.getElementById("password").value = "";
  } catch(err) {
    let err_msg = "resetPassword() - failed to access the value in the ";
    err_msg = "password input element\n" + err.message;
    throw new Exception(err_msg);
  }
}

function login() {
  try {
    let credentials = gatherCredentials();
    if(!credentialsLegal(credentials)) {
      resetCredentials();
      throw new Error("Some of the credentials, i.e. password, is illegal");
    }
    credentials_container.set(credentials);
    let encrypted_credentials = credentials_container.get();
    let url = "/login";
    postRequest(url, encrypted_credentials, function(status, response_text) {
      try {
        if(status == 200) {
          let response = JSON.parse(response_text);
          if(response["id"]) {
            credentials_container.setId(response["id"]);
            userHome(response["id"]);
            return;
          }
        }
        throw new Error("Server has refused the login attempt.\n" + response_text);
      } catch(err) {
        alert("Login attempt has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("Login attempt has failed. More details are in the console.");
    console.error(err.message);
  }
}

function signup() {
  try {
    let credentials = gatherCredentials();
    if(!credentialsLegal(credentials)) {
      resetCredentials();
      throw new Error("Some of the credentials, i.e. password, is illegal");
    }
    credentials_container.set(credentials);
    let encrypted_credentials = credentials_container.get();
    let url = "/signup";
    postRequest(url, encrypted_credentials, function(status, response_text) {
      try {
        if(status == 200) {
          let response = JSON.parse(response_text);
          if(response["id"]) {
            credentials_container.setId(response["id"]);
            alert("Your account has been created!\nYou will be redirected to your page.");
            userHome(response["id"]);
            return;
          }
        }
        throw new Error("Server has refused the signup attempt.\n" + response_text);
      } catch(err) {
        alert("Signup attempt has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("Singup attempt has failed. More details are in the console.");
    console.error(err.message);
  }
}

var credentials_container = credentialsContainerObj();