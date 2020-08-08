function enlargeImage(user_id, post_id, image_id) {
  let payload = credentials_container.get();
  xhr.open("POST", `/users/${user_id}/posts/${post_id}/images/${image_id}`, true);
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
    }
  };
  xhr.onerror = function(e) {
    alert(xhr.statusText);
  };
  xhr.send(payload);
}