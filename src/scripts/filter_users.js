function handleFilterUsersInput() {
  let element = document.getElementById("filter_users");
  if(element) {
    element.oninput = filterProvided;
  }
}

function filterProvided(element) {
  setTimeout(function() {
    let filter = element.target.value;
    if(filterLegal(filter)) {
      if(filter == "*") {
        filter = "";
      }
      else {
        filter = filter.split(' ').join('%20');
      }
      otherUsers(credentials_container.getId(), filter);
    }
    else {
      console.error("The values in the user search can be letters, * or whitespaces");
      element.target.value = "*";
      handleFilterUsersInput();
    }
  }, 1000);
}

function filterLegal(filter) {
  let legal_regex = new RegExp(/^\*$|^[a-zA-Z]+$|^[a-zA-Z]+\s$|^[a-zA-Z]+\s+[a-zA-Z]+$/g);
  return filter.match(legal_regex);
}

function otherUsers(user_id, filter) {
  let payload = credentials_container.get();
  let xhr = new XMLHttpRequest();
  xhr.open("POST", `/users/${user_id}/friends/new/${filter}`, true);
  xhr.setRequestHeader("Content-Type", "application/text");
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          console.error(response["reason"]);
          alert(response["message"]);
        } catch(err) {
          updateOtherUsersList(xhr.responseText);
          handleFilterUsersInput();
          return;
        }
      } else {
        console.error(xhr.statusText);
      }
      window.location.href = "http://localhost:8000/";
    }
  };
  xhr.onerror = function(e) {
    console.error(xhr.statusText);
    window.location.href = "http://localhost:8000/";
  };
  xhr.send(payload);
}

function updateOtherUsersList(list) {
  let element = document.getElementById("others_container");
  element.innerHTML = list;
  element.style.display = "none";
  element.style.display = "block";
}