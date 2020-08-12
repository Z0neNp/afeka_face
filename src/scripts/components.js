function newPostForm() {
  try {
    let payload = credentials_container.get();
    let user_id = credentials_container.getId();
    let url = `/users/${user_id}/posts/new`;
    postRequest(url, payload, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text[0] == "<") {
            document.getElementById("application").innerHTML = response_text;
            return;
          }
        }
        let err_msg = "Server has refused to provide the new post form.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("New post form request has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("New post form could not be loaded. More details are in the console");
    console.error(err.message);
  }
}

function userHome(user_id) {
  try {
    let payload = credentials_container.get();
    let url = `/users/${user_id}`;
    postRequest(url, payload, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text[0] == "<") {
            document.getElementById("application").innerHTML = response_text;
            updatePostsContainer();
            handleFilterUsersInput();
            return;
          }
        }
        let err_msg = "Server has refused to provide the user home page.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("User home page request has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("User home page could not be loaded. More details are in the console");
    console.error(err.message);
  }
}

function userFriend(user_id, friend_id) {
  try {
    let payload = credentials_container.get();
    let url = `/users/${user_id}/friends/${friend_id}`;
    postRequest(url, payload, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text[0] == "<") {
            document.getElementById("application").innerHTML = response_text;
            updateFriendPostsContainer(friend_id);
            return;
          }
        }
        let err_msg = "Server has refused to provide the user friend page.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("User friend page request has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("User friend page could not be loaded. More details are in the console");
    console.error(err.message);
  }
}

function userLogin() {
  try {
    let url = `/login`;
    getRequest(url, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text[0] == "<") {
            document.getElementById("application").innerHTML = response_text;
            return;
          }
        }
        let err_msg = "Server has refused to provide the user login page.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("User login page request has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("User login page could not be loaded. More details are in the console");
    console.error(err.message);
  }
}

function userSignup() {
  try {
    let url = `/signup`;
    getRequest(url, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text[0] == "<") {
            document.getElementById("application").innerHTML = response_text;
            return;
          }
        }
        let err_msg = "Server has refused to provide the user signup page.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("User signup page request has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("User signup page could not be loaded. More details are in the console");
    console.error(err.message);
  }
}