function enlargeImage(image_id) {
  try {
    let payload = credentials_container.get();
    let user_id = credentials_container.getId();
    let url = `/users/${user_id}/pictures/${image_id}`;
    postRequest(url, payload, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text[0] == "<") {
            document.getElementById("application").innerHTML = response_text;
            return;
          }
        }
        let err_msg = "Server has refused to provide the image enlargement.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("Image enlargement request has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("Image could not be enlarged. More details are in the console");
    console.error(err.message);
  }
}