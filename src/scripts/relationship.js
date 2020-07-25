function updateFriendStatus(user_id, friend_id, action) {
  let payload = undefined;
  let xhr = new XMLHttpRequest();
  payload = credentials_container.get();
  xhr.open("POST", `/users/${user_id}/friends/${action}/${friend_id}`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if(xhr.readyState === 4) {
      if(xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          alert(response["reason"] + "\n\n" + response["message"]);
        } catch(err) {
          if(xhr.responseText == "changed") {
            userHome(user_id);
          }
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