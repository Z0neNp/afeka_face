function updateFriendPostsContainer(friend_id) {
  let payload = credentials_container.get();
  let user_id = credentials_container.getId();
  let xhr = new XMLHttpRequest();
  xhr.open("POST", `/users/${user_id}/friends/${friend_id}/posts`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          alert(response["reason"] + "\n\n" + response["message"]);
        } catch(err) {
          document.getElementById("friend_posts").innerHTML = xhr.responseText;
          return;
        }
      } else {
        alert(xhr.statusText);
      }
      window.location.href = "http://localhost:8000/";
    }
  };
  xhr.onerror = function(e) {
    alert(xhr.statusText);
    window.location.href = "http://localhost:8000/";
  };
  xhr.send(payload);
}

function updatePostsContainer() {
  let payload = credentials_container.get();
  let user_id = credentials_container.getId();
  let xhr = new XMLHttpRequest();
  xhr.open("POST", `/users/${user_id}/posts`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          alert(response["reason"] + "\n\n" + response["message"]);
        } catch(err) {
          document.getElementById("user_posts").innerHTML = xhr.responseText;
          return;
        }
      } else {
        alert(xhr.statusText);
      }
      window.location.href = "http://localhost:8000/";
    }
  };
  xhr.onerror = function(e) {
    alert(xhr.statusText);
    window.location.href = "http://localhost:8000/";
  };
  xhr.send(payload);
}