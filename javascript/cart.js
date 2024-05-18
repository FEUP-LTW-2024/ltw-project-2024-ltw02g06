document.addEventListener("DOMContentLoaded", async () => {
  const removeBtns = document.querySelectorAll(
    "#cart-items-container li button"
  );
  const cartContainer = document.getElementById("cart");

  removeBtns.forEach((button) => {
    handleRemoveBtn(button);
  });

  const checkoutBtn = document.querySelector("#cart-checkout button");

  if (!checkoutBtn) return;

  checkoutBtn.addEventListener("click", async () => {
    try {
      await processCartPurchase();
      cartContainer.innerHTML =
        '<h2 id="empty-cart-title">O seu carrinho está vazio.</h2>';
      showModal("A sua compra foi realizada com sucesso!");
    } catch (error) {
      showModal(error.message);
    }
  });
});

const handleRemoveBtn = (button) => {
  const itemId = button.parentNode.dataset.cartItem;
  const itemPrice = button.parentNode.dataset.itemPrice;
  const itemShipping = button.parentNode.dataset.itemShipping;

  button.addEventListener("click", async () => {
    try {
      await removeFromCart(itemId);
      button.parentNode.remove();
      updateTotalPrices(itemPrice, itemShipping);
    } catch (error) {
      console.error("Error removing item from cart:", error);
    }
  });
};

const updateTotalPrices = (itemPrice, itemShipping) => {
  // Retrieve total price and total shipping elements
  const totalPriceElement = document
    .getElementById("cart-items-total-price")
    .querySelector("h4");
  const totalShippingElement = document
    .getElementById("cart-total-shipping-price")
    .querySelector("h4");
  const cartTotalPriceElement = document
    .getElementById("cart-total-price")
    .querySelector("h2");

  const currentTotalPrice = parseFloat(
    totalPriceElement.textContent.replace(" €", "")
  );
  const currentTotalShipping = parseFloat(
    totalShippingElement.textContent.replace(" €", "")
  );

  const totalPrice = currentTotalPrice - itemPrice;
  const totalShipping = currentTotalShipping - itemShipping;
  const cartTotalPrice = totalPrice + totalShipping;

  totalPriceElement.textContent = totalPrice.toFixed(2) + " €";
  totalShippingElement.textContent = totalShipping.toFixed(2) + " €";
  cartTotalPriceElement.textContent = cartTotalPrice.toFixed(2) + " €";

  const cartItems = document.querySelectorAll("#cart-items-container li");
  if (cartItems.length === 0) {
    const cartContainer = document.getElementById("cart");
    cartContainer.innerHTML =
      '<h2 id="empty-cart-title">O seu carrinho está vazio.</h2>';
  }
};

const removeFromCart = async (itemId) => {
  return fetch(`./../api/user/cart.php?item_id=${itemId}`, {
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

const handleItemBtn = (itemId) => {
  window.location.href = `../pages/item.php?id=${itemId}`;
};

const processCartPurchase = async () => {
  return fetch(`./../api/user/cart.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      checkout: true,
      csrf: csrf,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      response.json().then(() => {
        cartItemPrice = null;
      });
    })
    .catch(() => {
      throw new Error("Erro a processar compra. Tente novamente mais tarde.");
    });
};
