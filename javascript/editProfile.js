document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("new-image-input");
  const imagePreview = document.querySelector(
    "#edit-profile-image-container img"
  );
  const newImagePath = document.getElementById("new-image-path");

  fileInput.addEventListener("change", function () {
    const file = this.files[0];
    const reader = new FileReader();

    reader.onload = function () {
      imagePreview.src = reader.result;
      newImagePath.value = reader.result;
    };

    reader.readAsDataURL(file);
  });

  const editProfileCancelBtn = document.getElementById(
    "edit-profile-cancel-btn"
  );

  editProfileCancelBtn.addEventListener("click", () => {
    window.location.href = "../pages/profile.php";
  });
});
