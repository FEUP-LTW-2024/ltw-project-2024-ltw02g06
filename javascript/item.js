document.addEventListener("DOMContentLoaded", () => {
  handleWishlistBtn(sessionId, itemId);
  handleCartBtn(sessionId, itemId);
  handleEditBtn(itemId);
  handleDeleteBtn(itemId);
  handleMessageBtns(itemId, sellerId);
  handleImagesNavBtns();
});

const handleImagesNavBtns = () => {
  const previousBtn = document.getElementById("previous-image-btn");
  const nextBtn = document.getElementById("next-image-btn");
  const images = document.querySelectorAll("#item-image-container img");
  let currentIndex = 0;

  if (images.length == 0) {
    previousBtn.style.display = "none";
    nextBtn.style.display = "none";
    return;
  }

  // Event listener for previous button
  previousBtn.addEventListener("click", () => {
    images[currentIndex].style.display = "none";
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    images[currentIndex].style.display = "block";
  });

  // Event listener for next button
  nextBtn.addEventListener("click", () => {
    images[currentIndex].style.display = "none";
    currentIndex = (currentIndex + 1) % images.length;
    images[currentIndex].style.display = "block";
  });
};

const handleWishlistBtn = (sessionId, itemId) => {
  const addToWishlistBtn = document.getElementById("whishlist-btn");

  if (!addToWishlistBtn) return;

  const heartIcon = addToWishlistBtn.querySelector("ion-icon");

  addToWishlistBtn.addEventListener("click", () => {
    const isItemInWishlist = addToWishlistBtn.dataset.isItemInWishlist == "1";
    // Check if the user is authenticated
    if (sessionId == null) {
      // Redirect the user to the login page
      const currentPageUrl = window.location.pathname + window.location.search;
      window.location.href =
        "/pages/login.php?redirect=" + encodeURIComponent(currentPageUrl);
      return;
    }

    // Toggle item in the wishlist
    if (isItemInWishlist) {
      removeFromWishlist(itemId);
      heartIcon.setAttribute("name", "heart-outline");
    } else {
      addToWishlist(itemId);
      heartIcon.setAttribute("name", "heart");
    }

    addToWishlistBtn.dataset.isItemInWishlist = isItemInWishlist ? "0" : "1";
  });
};

const removeFromWishlist = (itemId) => {
  fetch(`./../api/user/wishlist.php?item_id=${itemId}`, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ csrf: csrf }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
    })
    .catch((error) => {
      console.error("Error removing item from wishlist:", error);
    });
};

const addToWishlist = (itemId) => {
  fetch(`./../api/user/wishlist.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ item_id: itemId, csrf: csrf }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
    })
    .catch((error) => {
      console.error("Error adding item to wishlist:", error);
    });
};

const handleCartBtn = (sessionId, itemId) => {
  const addToCartBtn = document.getElementById("add-to-cart-btn");

  if (!addToCartBtn) return;

  addToCartBtn.addEventListener("click", () => {
    const isItemInCart = addToCartBtn.dataset.isItemInCart == "1";
    // Check if the user is authenticated
    if (sessionId == null) {
      // Redirect the user to the login page
      const currentPageUrl = window.location.pathname + window.location.search;
      window.location.href =
        "/pages/login.php?redirect=" + encodeURIComponent(currentPageUrl);
      return;
    }

    // Toggle item in the cart
    if (isItemInCart) {
      removeFromCart(itemId);
      addToCartBtn.textContent = "Adicionar ao carrinho";
    } else {
      addToCart(itemId);
      addToCartBtn.textContent = "Remover do carrinho";
    }

    addToCartBtn.dataset.isItemInCart = isItemInCart ? "0" : "1";
  });
};

const removeFromCart = (itemId) => {
  fetch(`./../api/user/cart.php?item_id=${itemId}`, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ csrf: csrf }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
    })
    .catch((error) => {
      console.error("Error removing item from cart:", error);
    });
};

const addToCart = (itemId) => {
  fetch(`./../api/user/cart.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ item_id: itemId, csrf: csrf }),
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

const handleEditBtn = (itemId) => {
  const editBtn = document.getElementById("edit-btn");

  if (!editBtn) return;

  editBtn.addEventListener("click", () => {
    // Redirect the user to edit item page
    const currentPageUrl = window.location.pathname + window.location.search;
    window.location.href = `/pages/item.edit.php?id=${itemId}&redirect= ${encodeURIComponent(
      currentPageUrl
    )}`;
    return;
  });
};

const handleDeleteBtn = (itemId) => {
  const deleteBtn = document.getElementById("delete-btn");

  if (deleteBtn) {
    deleteBtn.addEventListener("click", () => {
      deleteItem(itemId, () => {
        // Redirect the user to seller page
        window.location.href = "/pages/seller.php";
      });
    });
  }

  const adminDeleteBtn = document.getElementById("admin-delete-btn");

  if (adminDeleteBtn) {
    adminDeleteBtn.addEventListener("click", () => {
      deleteItem(itemId, () => {
        // Redirect the user to seller page
        window.location.href = "/pages/items.php";
      });
    });
  }
};

const deleteItem = (itemId, callback) => {
  fetch(`./../api/item/index.php?id=${itemId}`, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ csrf: csrf }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      callback();
    })
    .catch((error) => {
      console.error("Error deleting item:", error);
    });
};

const handleMessageBtns = (itemId, sellerId) => {
  const negotiateBtn = document.getElementById("negotiate-btn");

  if (negotiateBtn) {
    negotiateBtn.addEventListener("click", () => {
      window.location.href = `/pages/chat.php?item=${itemId}&id=${sellerId}`;
    });
  }

  const sendMessageBtn = document.getElementById("send-message-btn");

  if (sendMessageBtn) {
    sendMessageBtn.addEventListener("click", () => {
      window.location.href = `/pages/chat.php?item=${itemId}&id=${sellerId}`;
    });
  }
};
