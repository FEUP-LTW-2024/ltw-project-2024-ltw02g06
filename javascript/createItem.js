const initialize = (categories) => {
  document.addEventListener("DOMContentLoaded", () => {
    handleCancelBtn();
    handleEditItemPrice();
    handleEditItemCategoryList(categories);
    handleImageInput();
  });
};

const handleCancelBtn = () => {
  const cancelButton = document.getElementById("edit-item-cancel-btn");

  cancelButton.addEventListener("click", () => {
    redirectToUrlWithRedirectParam();
  });
};

const redirectToUrlWithRedirectParam = () => {
  var redirectParam = getQueryParam("redirect");
  if (redirectParam) {
    var redirectUrl = decodeURIComponent(redirectParam);
    window.location.href = redirectUrl;
  } else {
    var redirectUrl = "/pages/seller.php";
    window.location.href = redirectUrl;
  }
};

const getQueryParam = (name) => {
  var urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(name);
};

const handleEditItemCategoryList = (categories) => {
  const categorySelect = document.getElementById("category");
  const attributeList = document.getElementById("edit-item-category-list");

  // Function to update attributes based on selected category
  const updateAttributes = () => {
    const selectedCategoryId = categorySelect.value;

    // Find selected category object
    const selectedCategory = categories[selectedCategoryId];

    // Clear existing attributes
    while (attributeList.childNodes.length > 2) {
      attributeList.removeChild(attributeList.lastChild);
    }

    // Add new attributes
    for (const attributeId in selectedCategory.attributes) {
      const attribute = selectedCategory.attributes[attributeId];
      const listItem = document.createElement("li");
      const label = document.createElement("label");
      label.setAttribute("for", attribute.name);
      label.textContent = attribute.name;
      listItem.appendChild(label);

      let inputElement;

      if (attribute.type === "int" || attribute.type === "default") {
        inputElement = document.createElement("input");
        inputElement.setAttribute("type", "text");
        inputElement.setAttribute("placeholder", attribute.name);
      } else if (attribute.type === "enum") {
        inputElement = document.createElement("select");

        attribute.values.forEach((value) => {
          const option = document.createElement("option");
          option.setAttribute("value", value.value);
          option.textContent = value.value;
          inputElement.appendChild(option);
        });
      }

      inputElement.setAttribute("name", `attributes[${attributeId}]`);
      inputElement.setAttribute("id", attribute.name);

      listItem.appendChild(inputElement);
      attributeList.appendChild(listItem);
    }
  };

  // Event listener for category select change
  categorySelect.addEventListener("change", updateAttributes);

  updateAttributes();
};

const handleEditItemPrice = () => {
  const editItemPrice = document.getElementById("edit-item-price");

  editItemPrice.addEventListener("input", (event) => {
    var inputValue = event.target.value;

    // Remove any non-digit or non-decimal characters except the first dot
    inputValue = inputValue.replace(/[^\d.]/g, "");

    // Ensure there's only one dot
    var dotIndex = inputValue.indexOf(".");
    if (dotIndex !== -1) {
      inputValue =
        inputValue.substr(0, dotIndex + 1) +
        inputValue.substr(dotIndex + 1).replace(/\./g, "");
    }

    // Remove leading zero if followed by another digit
    inputValue = inputValue.replace(/^0+(?=\d)/, "");

    // Ensure it's a positive number with up to two decimal places
    var decimalRegex = /^\d*\.?\d{0,2}$/;
    if (!decimalRegex.test(inputValue)) {
      // If not a valid number, set value to empty
      inputValue = "0";
    }

    // Update the input value
    editItemPrice.value = inputValue;
  });
};

const checkImageCount = () => {
  const images = document.querySelectorAll(
    "#edit-item-image-container > div:not(#new-image-container)"
  );
  const newImageContainer = document.getElementById("new-image-container");

  if (images.length > 8) {
    newImageContainer.style.display = "none";
  } else {
    newImageContainer.style.display = "flex";
  }
};

const handleImageInput = () => {
  const addButton = document.getElementById("accept-preview-image-btn");
  addButton.addEventListener("click", () => {
    checkImageCount();
  });

  checkImageCount();
};

const previewImage = (event) => {
  const file = event.target.files[0];
  const reader = new FileReader();

  reader.onload = () => {
    const preview = document.getElementById("preview-new-image");
    const img = document.createElement("img");
    img.src = reader.result;
    preview.innerHTML = "";
    preview.style.backgroundColor = "transparent";
    preview.appendChild(img);
    document.querySelector(
      "#new-image-container > div:not(#preview-new-image)"
    ).style.display = "flex";
    document.querySelector(
      '#new-image-container label[for="new-image-input"]'
    ).style.display = "none";
  };

  reader.readAsDataURL(file);
};

const closePreview = () => {
  const preview = document.getElementById("preview-new-image");
  preview.style.backgroundColor = "var(--background)";
  preview.innerHTML = "";
  const otherDiv = document.querySelector(
    "#new-image-container > div:not(#preview-new-image)"
  );
  otherDiv.style.display = "none";
  document.getElementById("new-image-input").value = "";
  const label = document.querySelector(
    '#new-image-container label[for="new-image-input"]'
  );
  label.style.display = "flex";
};

const acceptPreview = () => {
  const previewImg = document
    .getElementById("preview-new-image")
    .querySelector("img");
  const container = document.getElementById("edit-item-image-container");
  const div = document.createElement("div");
  const img = document.createElement("img");
  const button = document.createElement("button");
  const input = document.createElement("input");

  button.setAttribute("type", "button");
  button.innerHTML = '<ion-icon name="trash-outline"></ion-icon>';
  button.onclick = () => {
    removeImage(button);
  };

  img.src = previewImg.src;
  img.alt = "Item Image";

  input.setAttribute("type", "hidden");
  input.setAttribute("name", "images[]");
  input.setAttribute("value", previewImg.src);

  div.appendChild(img);
  div.appendChild(button);
  div.appendChild(input);
  container.insertBefore(div, container.lastElementChild);
  closePreview();
};

const removeImage = (button) => {
  button.parentNode.remove();
};
