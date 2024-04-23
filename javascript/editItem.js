document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("edit-item-price")
    .addEventListener("change", function () {
      this.value = parseFloat(this.value).toFixed(2);
    });

  // TEXT AREA CODE (Description) - TODO: clean up code
  const textarea = document.getElementById("edit-item-description");
  // Add an event listener for change event
  textarea.addEventListener("input", function () {
    // Get the text inside the textarea
    var text = textarea.value;
    // Print the text
    text = text.replace(/\n/g, "<br>");
    text = DOMPurify.sanitize(text, { ALLOWED_TAGS: ["br"] });
  });

  const editItemPrice = document.getElementById("edit-item-price");

  editItemPrice.addEventListener("input", function (event) {
    // Get the input value
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
});

function previewImage(event) {
  const file = event.target.files[0];
  const reader = new FileReader();

  reader.onload = function () {
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
}

function closePreview() {
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
}

function acceptPreview() {
  const previewImg = document
    .getElementById("preview-new-image")
    .querySelector("img");
  const container = document.getElementById("edit-item-image-container");
  const div = document.createElement("div");
  const img = document.createElement("img");
  const button = document.createElement("button");
  button.innerHTML = '<ion-icon name="trash-outline"></ion-icon>';
  button.onclick = function () {
    removeImage(this);
  };
  img.src = previewImg.src;
  img.alt = "Item Image";
  div.appendChild(img);
  div.appendChild(button);
  container.insertBefore(div, container.lastElementChild);
  closePreview();
}

function removeImage(button) {
  button.parentNode.remove();
}
