const initialize = (sessionId, itemId) => {
  document.addEventListener("DOMContentLoaded", function () {
    handleWishlistBtn(sessionId, itemId);

    const previousBtn = document.getElementById("previous-image-btn");
    const nextBtn = document.getElementById("next-image-btn");
    const images = document.querySelectorAll("#item-image-container img");
    let currentIndex = 0;

    // Event listener for previous button
    previousBtn.addEventListener("click", function () {
      if (images.length == 0) return;
      images[currentIndex].style.display = "none";
      currentIndex = (currentIndex - 1 + images.length) % images.length;
      images[currentIndex].style.display = "block";
    });

    // Event listener for next button
    nextBtn.addEventListener("click", function () {
      if (images.length == 0) return;
      images[currentIndex].style.display = "none";
      currentIndex = (currentIndex + 1) % images.length;
      images[currentIndex].style.display = "block";
    });
  });
};

const handleWishlistBtn = (sessionId, itemId) => {
  const addToWishlistBtn = document.getElementById("whishlist-btn");
  const heartIcon = addToWishlistBtn.querySelector("ion-icon");

  addToWishlistBtn.addEventListener("click", function () {
    const isItemInWishlist = addToWishlistBtn.dataset.isItemInWishlist == "1";
    // Check if the user is authenticated
    if (sessionId == null) {
      // Redirect the user to the login page
      const currentPageUrl = window.location.href;
      window.location.href =
        "/login.php?redirect=" + encodeURIComponent(currentPageUrl);
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

function removeFromWishlist(itemId) {
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
}

function addToWishlist(itemId) {
  fetch(`./../api/user/wishlist.php`, {
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
      console.log("Item added to wishlist");
    })
    .catch((error) => {
      console.error("Error adding item to wishlist:", error);
    });
}
