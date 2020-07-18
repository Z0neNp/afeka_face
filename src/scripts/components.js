function userHome(user_id) {
  let payload = credentials_container.get();
  let xhr = new XMLHttpRequest();
  xhr.open("POST", `/users/${user_id}`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        document.getElementById("application").innerHTML = xhr.responseText;
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

function userFriend(user_id, friend_id) {
  let payload = credentials_container.get();
  let xhr = new XMLHttpRequest();
  xhr.open("POST", `/users/${user_id}/friends/${friend_id}`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        document.getElementById("application").innerHTML = xhr.responseText;
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