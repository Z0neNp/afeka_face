function handleFilterUsersInput() {
  try {
    let element = document.getElementById("filter_users");
    element.oninput = filterProvided;
  } catch(err) {
    throw Error("Failed to setup Friends API element.\n" + err.message);
  }
}

function filterProvided(element) {
  setTimeout(function() {
    try {
      let filter = element.target.value;
      if(filterLegal(filter)) {
        if(filter == "*") {
          filter = "";
        }
        else {
          filter = filter.split(' ').join('%20');
        }
        otherUsers(filter);
      }
      else {
        throw new Error("The values in the user search can be letters, * or whitespace");
      }
    } catch(err) {
      alert("Failed to use the Friends API. More details in the console");
      console.error(err.message);
    }
  }, 1000);
}

function filterLegal(filter) {
  try {
    let legal_regex = new RegExp(/^\*$|^[a-zA-Z]+$|^[a-zA-Z]+\s$|^[a-zA-Z]+\s+[a-zA-Z]+$/g);
    return filter.match(legal_regex);
  } catch(err) {
    throw new Error("filterLegal() - failed to make the validation");
  }
}

function otherUsers(filter) {
  try {
    let payload = credentials_container.get();
    let user_id = credentials_container.getId();
    let url = `/users/${user_id}/friends/new/${filter}`;
    postRequest(url, payload, function(status, response_text) {
      try {
        if (status === 200) {
          if(response_text[0] == "<") {
            updateOtherUsersList(response_text);
            handleFilterUsersInput();
            return;
          }
        }
        let err_msg = "Server has refused to provide the other users for the Friends API.\n";
        throw new Error(err_msg + response_text);
      } catch(err) {
        alert("Other users request from Friends API has failed. More details are in the console.");
        console.error(err.message);
      }
    });
  } catch(err) {
    alert("Other users request from Friends API has failed. More details in the console");
    console.error(err.message);
  }
}

function updateOtherUsersList(list) {
  try {
    let element = document.getElementById("others_container");
    element.innerHTML = list;
    element.style.display = "none";
    element.style.display = "block";
  } catch(err) {
    throw new Error("updateOtherUsersList() - failed.\n" + err.message);
  }
}