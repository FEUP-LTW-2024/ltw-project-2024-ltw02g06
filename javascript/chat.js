document.addEventListener("DOMContentLoaded", async () => {
  const chatMessagesContainer = document.getElementById("chat-messages");
  chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;

  const priceInput = document.querySelector("#chat form input:first-of-type");
  const messageInput = document.querySelector("#chat form input:last-of-type");
  const messageForm = document.querySelector("#chat form");

  handleChatHeader();
  handleSendMessage(priceInput, messageInput, messageForm);
  handlePriceInput(priceInput);
  const chat = await fetchChat();
  renderChat(chat);
});

const handleChatHeader = () => {
  const chatItem = document.querySelector(".chat-item");
  const chatOtherUser = document.querySelector(".chat-other-user");

  chatItem.addEventListener("click", () => {
    window.location.href = `/pages/item.php?id=${itemId}`;
    return;
  });

  chatOtherUser.addEventListener("click", () => {
    window.location.href = `/pages/profile.php?id=${otherUserId}`;
    return;
  });
};

const handleSendMessage = async (priceInput, messageInput, messageForm) => {
  messageForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target); // Get form data
    const response = await fetch("../actions/action_send_message.php", {
      method: "POST",
      body: formData,
    });
    if (response.ok) {
      // Handle success
      priceInput.value = "";
      messageInput.value = "";
    } else {
      // Handle error
      console.error("Failed to send message");
    }

    const chat = await fetchChat();
    renderChat(chat);
  });
};

const renderMessage = (message, userId, itemId) => {
  const li = document.createElement("li");

  if (message.type === "negotiation") {
    li.classList.add(
      message.receiver === userId ? "received-proposition" : "sent-proposition"
    );

    // Create proposition value div
    const propositionValueDiv = document.createElement("div");
    propositionValueDiv.classList.add("proposition-value");
    propositionValueDiv.innerHTML = `
      <h3>Proposta: ${message.value} €</h3>
      <span>${getTimeAgo(message.timestamp)}</span>
    `;
    li.appendChild(propositionValueDiv);

    // Create message paragraph
    const messageText = document.createElement("p");
    messageText.textContent = message.message;
    li.appendChild(messageText);

    // Create proposition buttons div
    const propositionBtnsDiv = document.createElement("div");
    propositionBtnsDiv.classList.add("proposition-btns");

    if (message.accepted === null) {
      if (message.item_seller === userId) {
        const rejectBtn = document.createElement("button");
        rejectBtn.classList.add("reject-proposition-btn");
        rejectBtn.textContent = "Rejeitar";
        const rejectIcon = document.createElement("ion-icon");
        rejectIcon.setAttribute("name", "close");
        rejectBtn.appendChild(rejectIcon);

        const acceptBtn = document.createElement("button");
        acceptBtn.classList.add("accept-proposition-btn");
        acceptBtn.textContent = "Aceitar";
        const acceptIcon = document.createElement("ion-icon");
        acceptIcon.setAttribute("name", "checkmark");
        acceptBtn.appendChild(acceptIcon);

        rejectBtn.addEventListener("click", async () => {
          updateMessage(message.id, false);
          const chat = await fetchChat();
          renderChat(chat);
        });

        acceptBtn.addEventListener("click", async () => {
          updateMessage(message.id, true);
          const chat = await fetchChat();
          renderChat(chat);
        });

        propositionBtnsDiv.appendChild(rejectBtn);
        propositionBtnsDiv.appendChild(acceptBtn);
      } else {
        propositionBtnsDiv.innerHTML = "<h5>Por decidir</h5>";
      }
    } else if (message.accepted) {
      if (message.item_seller === userId) {
        propositionBtnsDiv.innerHTML = "<h5>Aceite</h5>";
      } else {
        if (cartItemPrice == message.value) {
          const addToCartBtn = document.createElement("button");
          addToCartBtn.classList.add("add-to-cart-proposition-btn");
          addToCartBtn.textContent = "Remover do carrinho";
          const cartIcon = document.createElement("ion-icon");
          cartIcon.setAttribute("name", "cart");
          addToCartBtn.appendChild(cartIcon);

          addToCartBtn.addEventListener("click", async () => {
            await removeFromCart(itemId);
            const chat = await fetchChat();
            renderChat(chat);
          });

          propositionBtnsDiv.appendChild(addToCartBtn);
        } else {
          const addToCartBtn = document.createElement("button");
          addToCartBtn.classList.add("add-to-cart-proposition-btn");
          addToCartBtn.textContent = "Adicionar ao carrinho";
          const cartIcon = document.createElement("ion-icon");
          cartIcon.setAttribute("name", "cart-outline");
          addToCartBtn.appendChild(cartIcon);

          addToCartBtn.addEventListener("click", async () => {
            await addToCart(itemId, message.id, message.value);
            const chat = await fetchChat();
            renderChat(chat);
          });

          propositionBtnsDiv.appendChild(addToCartBtn);
        }
      }
    } else {
      propositionBtnsDiv.innerHTML = "<h5>Rejeitado</h5>";
    }

    li.appendChild(propositionBtnsDiv);
  } else {
    li.classList.add(
      message.receiver === userId ? "received-message" : "sent-message"
    );
    const messageText = document.createElement("p");
    messageText.textContent = `${message.message} `;
    const timeSpan = document.createElement("span");
    timeSpan.textContent = getTimeAgo(message.timestamp);
    messageText.appendChild(timeSpan);
    li.appendChild(messageText);
  }

  return li;
};

const renderChat = async (chat) => {
  if (chat.length === 0) {
    return;
  }

  const chatMessagesContainer = document.getElementById("chat-messages");
  chatMessagesContainer.innerHTML = "";

  const chatContainer = document.getElementById("chat");
  const startChatTitle = document.getElementById("start-chat");
  if (startChatTitle) chatContainer.removeChild(startChatTitle);

  chat.reverse().forEach((message) => {
    const messageElement = renderMessage(message, userId, itemId);
    chatMessagesContainer.appendChild(messageElement);
  });

  // Scroll to the bottom of the chat container
  chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
};

const updateMessage = (messageId, accepted) => {
  fetch(`./../api/inbox/message.php`, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      message_id: messageId,
      accepted: accepted,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
    })
    .catch((error) => {
      console.error("Error adding item to cart:", error);
    });
};

const addToCart = async (itemId, messageId) => {
  return fetch(`./../api/user/cart.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      item_id: itemId,
      message_id: messageId,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      response.json().then((data) => {
        cartItemPrice = data.cartItem.price;
      });
    })
    .catch((error) => {
      console.error("Error adding item to cart:", error);
    });
};

const removeFromCart = async (itemId) => {
  return fetch(`./../api/user/cart.php?item_id=${itemId}`, {
    method: "DELETE",
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      response.json().then(() => {
        cartItemPrice = null;
      });
    })
    .catch((error) => {
      console.error("Error removing item from cart:", error);
    });
};

const fetchChat = async () => {
  return fetch(`./../api/inbox/chat.php?item=${itemId}&id=${otherUserId}`, {
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

const handlePropositionButtons = () => {
  const rejectPropositionBtn = document.getElementById(
    "reject-proposition-btn"
  );
  const acceptPropositionBtn = document.getElementById(
    "accept-proposition-btn"
  );

  rejectPropositionBtn.addEventListener("click", () => {
    rejectProposition();
  });

  acceptPropositionBtn.addEventListener("click", () => {
    acceptProposition();
  });
};
