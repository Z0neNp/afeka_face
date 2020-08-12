function gatherImages() {
  try {
    let result = [];
    for(let i = 0; i < 6; i++) {
      let container_id = `post_image_${i}`;
      let url = document.getElementById(container_id).value;
      if(url && url.length > 0) {
        result.push(url);
      }
    }
    return result;
  } catch(err) {
    throw new Exception("gatherImages() - failed.\n" + err.message);
  }
}

function gatherMessage() {
  try {
    return document.getElementById("post_message").value;
  } catch(err) {
    throw new Error("gatherMessage() - failed.\n" + err.message);
  }
}

function gatherPostData() {
  try {
    let result = {
      message: gatherMessage(),
      thumbnail: gatherPostThumbnail(),
      images: gatherImages(),
      private: "no"
    }
    if(document.getElementById("post_private").checked) {
      result.private = "yes";
    }
    return result;
  } catch(err) {
    throw new Error("gatherPostData() - failed.\n" + err.message);
  }
}

function gatherPostThumbnail() {
  try {
    return document.getElementById("post_thumbnail").value;
  } catch(err) {
    throw new Error("gatherPostThumbnail() - failed.\n" + err.message);
  }
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
  try {
    let url_regex = new RegExp(/^http:\/\/.*|^https:\/\/.*$/g);
    if(obj.length > 0) {
      return obj.match(url_regex);
    }
    return true;
  } catch(err) {
    throw new Error("postImageLegal() - failed to make the validation.\n" + err.message);
  }
}

function postFinalizeData(post) {
  if(post.images && post.images.length == 0) {
    delete post.images;
  }
}

function postMessageLegal(obj) {
  try {
    let message_regex = new RegExp(/^[a-zA-Z0-9\t.,;]+$/g);
    return obj.match(message_regex);
  } catch(err) {
    throw new Error("postMessageLegal() - failed to make the validation.\n" + err.message);
  }
}

function resetPostData() {
  try {
    for(let i = 0; i < 5; i++) {
      document.getElementById(`post_image_${i}`).value = "";
      document.getElementById("post_message").value = "";
      document.getElementById("post_thumbnail").value = "";
      document.getElementById("post_private").checked = false;
    }
  } catch(err) {
    throw new Error("resetPostData() - failed.\n" + err.message);
  }
}

function submitNewPostData() {
  try {
    let post = gatherPostData();
    postDataLegal(post);
    postFinalizeData(post);
    let credentials = credentials_container.get();
    let user_id = credentials_container.getId();
    let payload = {
      post: post,
      credentials: credentials
    }
    payload = JSON.stringify(payload);
    let url = `/users/${user_id}/posts/add`;
    postRequest(url, payload, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text[0] != "{") {
            userHome(user_id);
            return;
          }
        }
        throw new Error("The server has refused the new post.\n" + response_text);
      } catch(err) {
        alert("New post submission request has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("Failed to submit the new post request. More details are in console.");
    console.error(err.message);
    try {
      resetPostData();
    } catch(err) {
      newPostForm();
    }
  }
}

function updateFriendPostsContainer(friend_id) {
  try {
    let payload = credentials_container.get();
    let user_id = credentials_container.getId();
    let url = `/users/${user_id}/friends/${friend_id}/posts`;
    postRequest(url, payload, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text[0] == "<") {
            document.getElementById("friend_posts").innerHTML = response_text;
            return;
          }
        }
        let err_msg = "Server has refused to provide the friend posts.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("Friend posts could not be loaded. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("Friend posts could not be loaded. More details are in the console");
    console.error(err.message);
  }
}

function updatePostsContainer() {
  try {
    let payload = credentials_container.get();
    let user_id = credentials_container.getId();
    let url = `/users/${user_id}/posts`;
    postRequest(url, payload, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text[0] == "<") {
            document.getElementById("user_posts").innerHTML = response_text;
            return;
          }
        }
        let err_msg = "Server has refused to provide the posts.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("Posts could not be loaded. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("Posts could not be loaded. More details are in the console");
    console.error(err.message);
  }
}