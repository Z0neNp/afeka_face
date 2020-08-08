function gatherImages() {
  let result = [];
  for(let i = 0; i < 6; i++) {
    let container_id = `post_image_${i}`;
    let url = document.getElementById(container_id).value;
    if(url && url.length > 0) {
      result.push(url);
    }
  }
  return result;
}

function gatherPostData() {
  let result = {
    message: document.getElementById("post_message").value,
    thumbnail: document.getElementById("post_thumbnail").value,
    images: gatherImages(),
    private: "no"
  }
  if(document.getElementById("post_private").checked) {
    result.private = "yes";
  }
  postDataLegal(result);
  postFinalizeData(result);
  return result;
}

function postDataLegal(post) {
  if(post.message) {
    if(!postMessageLegal(post.message)) {
      throw new Error("Post message is illegal");
    }
  }
  if(post.thumbnail) {
    if(!postImageLegal(post.thumbnail)) {
      throw new Error("Post thumbnail is illegal");
    }
  }
  if(post.images) {
    for(let i = 0; i < post.images.length; i++) {
      if(!postImageLegal(post.images[i])) {
        throw new Error("Post image is illegal");
      }
    }
  }
}

function postImageLegal(obj) {
  let url_regex = new RegExp(/^http:\/\/.*|^https:\/\/.*$/g);
  if(obj.length > 0) {
    return obj.match(url_regex);
  }
  return true;
}

function postFinalizeData(post) {
  if(post.images && post.images.length == 0) {
    delete post.images;
  }
}

function postMessageLegal(obj) {
  let message_regex = new RegExp(/^[a-zA-Z0-9\t.,;]+$/g);
  return obj.match(message_regex);
}

function resetPostData() {
  for(let i = 0; i < 5; i++) {
    document.getElementById(`post_image_${i}`).value = "";
    document.getElementById("post_message").value = "";
    document.getElementById("post_thumbnail").value = "";
    document.getElementById("post_private").checked = false;
  }
}

function submitNewPostData() {
  let credentials = undefined;
  let payload = undefined;
  let post = undefined;
  let user_id = undefined;
  let xhr = new XMLHttpRequest();
  try {
    post = gatherPostData();
    credentials = credentials_container.get();
    user_id = credentials_container.getId();
    payload = {
      post: post,
      credentials: credentials
    }
    payload = JSON.stringify(payload);
  } catch(err) {
    alert(`Failed to send the new post data.\n${err.message}\nTry again!`);
    try {
      resetPostData();
    } catch(err) {
      newPostForm();
    }
    return;
  }
  xhr.open("POST", `/users/${user_id}/posts/add`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          alert(response["reason"] + "\n\n" + response["message"]);
        } catch(err) {
          userHome(user_id);
          return;
        }
      } else {
        alert(xhr.statusText);
      }
    }
  };
  xhr.onerror = function(e) {
    alert(xhr.statusText);
  };
  xhr.send(payload);
}

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