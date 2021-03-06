function getRequest(url, callback) {
  try {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.setRequestHeader("Content-Type", "application/text");
    xhr.onload = function() {
      if(xhr.readyState === 4) {
        callback(xhr.status, xhr.responseText);
      }
    }
    xhr.onerror = function() {
      callback(xhr.status, xhr.statusText);
    };
    xhr.send(null);
  } catch(err) {
    let err_msg = "loginRequest() - failed\n" + err.message();
    throw new Exception(err_msg);
  }
}

function postRequest(url, payload, callback) {
  try {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/text");
    xhr.onload = function() {
      if(xhr.readyState === 4) {
        callback(xhr.status, xhr.responseText);
      }
    }
    xhr.onerror = function() {
      callback(xhr.status, xhr.statusText);
    };
    xhr.send(payload);
  } catch(err) {
    let err_msg = "postRequest() - failed\n" + err.message();
    throw new Exception(err_msg);
  }
}