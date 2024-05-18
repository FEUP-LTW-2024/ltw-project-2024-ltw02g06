document.addEventListener("DOMContentLoaded", () => {
  handleSearchBar();
  searchInbox();
});

const handleSearchBar = () => {
  const queryString = window.location.search;
  const params = new URLSearchParams(queryString);

  let search = params.get("search");
  if (!search || isNaN(search)) {
    search = null;
    deleteParam("search");
  }

  const searchInput = document.querySelector("#small-search-bar > input");

  searchInput.value = search;
  searchInput.addEventListener("input", () => {
    if (searchInput.value.trim() == "") deleteParam("search");
    else updateParam("search", searchInput.value.trim());
    searchInbox();
  });

  const searchBtn = document.querySelector(
    "#small-search-bar-container > button"
  );
  searchBtn.addEventListener("click", searchInbox);
};

const searchInbox = async () => {
  const inbox = await getInbox();
  renderInbox(inbox);
};

const renderInbox = (inbox) => {
  const inboxContainer = document.getElementById("inbox-container");
  inboxContainer.innerHTML = "";

  if (inbox.length == 0) {
    const li = document.createElement("li");
    const h2 = document.createElement("h2");
    h2.textContent = "Não encontramos resultados!";
    h2.style.textAlign = "center";
    h2.style.marginTop = "10rem";
    h2.style.marginBottom = "10rem";
    li.appendChild(h2);
    li.style.justifyContent = "center";
    li.style.alignItems = "center";
    li.style.height = "auto";
    inboxContainer.appendChild(li);
    return;
  }

  for (const key in inbox) {
    if (inbox.hasOwnProperty(key)) {
      const chatData = inbox[key];
      const li = renderChat(chatData);
      inboxContainer.appendChild(li);
    }
  }
};

const renderChat = (chatData) => {
  const li = document.createElement("li");

  const inboxChatItem = document.createElement("div");
  const itemName = document.createElement("h3");
  const div1 = document.createElement("div");
  const itemPrice = document.createElement("h3");
  const inboxChatMsg = document.createElement("div");
  const div2 = document.createElement("div");
  const userName = document.createElement("h4");
  const timestamp = document.createElement("span");
  const div3 = document.createElement("div");
  const sender = document.createElement("span");
  const message = document.createElement("p");

  inboxChatItem.className = "inbox-chat-item";
  inboxChatMsg.className = "inbox-chat-msg";

  inboxChatItem.style.cursor = "pointer";
  inboxChatItem.addEventListener("click", () => {
    window.location.href = `/pages/item.php?id=${chatData[0].item_id}
    }`;
  });

  inboxChatMsg.style.cursor = "pointer";
  inboxChatMsg.addEventListener("click", () => {
    window.location.href = `/pages/chat.php?item=${chatData[0].item_id}&id=${
      chatData[0].sender == userId ? chatData[0].receiver : chatData[0].sender
    }`;
  });

  itemName.textContent = chatData[0].item_name;
  itemPrice.textContent = `${chatData[0].item_price} €`;
  inboxChatItem.appendChild(itemName);
  div1.appendChild(itemPrice);
  inboxChatItem.appendChild(div1);

  userName.textContent =
    chatData[0].receiver == userId
      ? `${chatData[0].sender_first_name} ${chatData[0].sender_last_name}`
      : `${chatData[0].receiver_first_name} ${chatData[0].receiver_last_name}`;
  timestamp.textContent = getTimeAgo(chatData[0].timestamp);
  div2.appendChild(userName);
  div2.appendChild(timestamp);

  sender.textContent =
    chatData[0].sender == userId ? "Eu:" : `${chatData[0].sender_first_name}:`;
  message.textContent = chatData[0].message;
  div3.appendChild(sender);
  div3.appendChild(message);

  inboxChatMsg.appendChild(div2);
  inboxChatMsg.appendChild(div3);

  li.appendChild(inboxChatItem);
  li.appendChild(inboxChatMsg);

  return li;
};

const getInbox = async () => {
  const queryString = window.location.search;
  const separator = queryString ? "&" : "?";
  const url = `./../api/inbox/index.php${queryString}${separator}csrf=${csrf}`;

  return fetch(url, {
    method: "GET",
  })
    .then((response) => {
      if (response.status == 404) {
        return [];
      } else if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .catch((error) => {
      console.error("There was an unexpected error:", error);
    });
};

const updateParam = (param, value) => {
  const url = new URL(window.location.href);
  url.searchParams.set(param, value);
  history.pushState({}, "", url.toString());
};

const deleteParam = (param) => {
  const url = new URL(window.location.href);
  url.searchParams.delete(param);
  history.pushState({}, "", url.toString());
};
