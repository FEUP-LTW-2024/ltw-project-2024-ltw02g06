document.addEventListener("DOMContentLoaded", async () => {
  const wishlistBtns = document.querySelectorAll(".wishlist-btn");
  const cartBtns = document.querySelectorAll(".cart-btn");

  wishlistBtns.forEach((button) => {
    handleWishlistBtn(button);
  });

  cartBtns.forEach((button) => {
    handleCartBtn(button);
  });
});

const handleWishlistBtn = (button) => {
  const li = button.closest("li");
  const itemId = li.dataset.itemId;
  button.addEventListener("click", async () => {
    try {
      await removeFromWishlist(itemId);
      li.remove();
      const wishlistItems = document.querySelectorAll("#items-container li");
      if (wishlistItems.length === 0) {
        const wishlistContainer = document.getElementById("items");
        wishlistContainer.innerHTML =
          '<h2 id="empty-cart-title">A sua wishlist está vazia.</h2>';
      }
    } catch (error) {
      console.error("Error removing item from wishlist:", error);
    }
  });
};

const removeFromWishlist = async (itemId) => {
  fetch(`./../api/user/wishlist.php?item_id=${itemId}`, {
    method: "DELETE",
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      console.log("Item removed from wishlist");
    })
    .catch((error) => {
      console.error("Error removing item from wishlist:", error);
    });
};

const handleCartBtn = (button) => {
  const li = button.closest("li");
  const itemId = li.dataset.itemId;
  let isItemInCart = li.dataset.itemInCart === "1";
  const icon = button.querySelector("ion-icon");

  button.addEventListener("click", async () => {
    if (isItemInCart) {
      await removeFromCart(itemId);
      icon.setAttribute("name", "cart-outline");
    } else {
      await addToCart(itemId);
      icon.setAttribute("name", "cart");
    }

    isItemInCart = !isItemInCart;
    li.dataset.itemInCart = isItemInCart ? "1" : "0";
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
    })
    .catch((error) => {
      console.error("Error removing item from cart:", error);
    });
};

const addToCart = async (itemId) => {
  fetch(`./../api/user/cart.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ item_id: itemId }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      console.log("Item added to cart");
    })
    .catch((error) => {
      console.error("Error adding item to cart:", error);
    });
};
