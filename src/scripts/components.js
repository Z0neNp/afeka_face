function userHome(user_id) {
  let payload = credentials_container.get();
  let xhr = new XMLHttpRequest();
  xhr.open("POST", `/users/${user_id}`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          alert(response["reason"] + "\n\n" + response["message"]);
        } catch(err) {
          document.getElementById("application").innerHTML = xhr.responseText;
          updatePostsContainer();
          handleFilterUsersInput();
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

function userFriend(user_id, friend_id) {
  let payload = credentials_container.get();
  let xhr = new XMLHttpRequest();
  xhr.open("POST", `/users/${user_id}/friends/${friend_id}`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          alert(response["reason"] + "\n\n" + response["message"]);
        } catch(err) {
          document.getElementById("application").innerHTML = xhr.responseText;
          updateFriendPostsContainer(friend_id);
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

function userLogin() {
  let xhr = new XMLHttpRequest();
  xhr.open("GET", `/login`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          alert(response["reason"] + "\n\n" + response["message"]);
        } catch(err) {
          document.getElementById("application").innerHTML = xhr.responseText;
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
  xhr.send(null);
}

function userSignup() {
  let xhr = new XMLHttpRequest();
  xhr.open("GET", `/signup`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          alert(response["reason"] + "\n\n" + response["message"]);
        } catch(err) {
          document.getElementById("application").innerHTML = xhr.responseText;
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
  xhr.send(null);
}