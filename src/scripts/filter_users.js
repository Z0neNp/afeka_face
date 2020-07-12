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
      otherUsers(filter);
    }
    else {
      console.error("The values in the user search can be letters, * or whitespaces");
      element.target.value = "*";
    }
  }, 750);
}

function filterLegal(filter) {
  let legal_regex = new RegExp(/^\*$|^[a-zA-Z]+$|^[a-zA-Z]+\s$|^[a-zA-Z]+\s+[a-zA-Z]+$/g);
  return filter.match(legal_regex);
}

function otherUsers(filter) {
  let xhr = new XMLHttpRequest();
  xhr.open("GET", `${window.location.href}/friends/new/${filter}`, true);
  xhr.onload = function(e) {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        updateOtherUsersList(xhr.responseText);
        handleFilterUsersInput();
      } else {
        console.error(xhr.statusText);
      }
    }
  };
  xhr.onerror = function(e) {
    console.error(xhr.statusText);
  };
  xhr.send(null);
}

function updateOtherUsersList(list) {
  let element = document.getElementById("others_container");
  element.innerHTML = list;
  element.style.display = "none";
  element.style.display = "block";
}

handleFilterUsersInput();