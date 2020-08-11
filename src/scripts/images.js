function enlargeImage(image_id) {
  let xhr = new XMLHttpRequest();
  let payload = credentials_container.get();
  let user_id = credentials_container.getId();
  xhr.open("POST", `/users/${user_id}/pictures/${image_id}`, true);
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