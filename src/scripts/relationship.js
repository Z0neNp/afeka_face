function updateFriendStatus(user_id, friend_id, action) {
  try {
    let payload = credentials_container.get();
    let url = `/users/${user_id}/friends/${action}/${friend_id}`;
    postRequest(url, payload, function(status, response_text) {
      try {
        if(status == 200) {
          if(response_text == "changed") {
            userHome(user_id);
            return;
          }
        }
        let err_msg = "Server has refused to update the friend status.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("Friend status could not be updated. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("Friend status could not be updated. More details are in the console");
    console.error(err.message);
  }
}