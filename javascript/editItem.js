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
