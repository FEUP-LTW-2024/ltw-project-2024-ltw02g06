document.addEventListener("DOMContentLoaded", function () {
  // Handle profile image input
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

  // Handle cancel button
  const editProfileCancelBtn = document.getElementById(
    "edit-profile-cancel-btn"
  );
  editProfileCancelBtn.addEventListener("click", () => {
    window.location.href = "../pages/profile.php";
  });

  // Handle email input
  const emailInput = document.querySelector(
    "#edit-profile-name input[name='email']"
  );
  validateEmailInput(emailInput);

  // Handle form submit
  handleFormSubmit();
});

const handleFormSubmit = () => {
  const emailInput = document.querySelector(
    "#edit-profile-name input[name='email']"
  );
  const editProfileForm = document.getElementById("edit-profile");
  editProfileForm.addEventListener("submit", async (event) => {
    event.preventDefault(); // Prevent default form submission

    const inputs = editProfileForm.querySelectorAll("input[type='text']");
    let isEmpty = false;

    inputs.forEach((input) => {
      if (input.value.trim() === "") {
        isEmpty = true;
      }
    });

    if (isEmpty) {
      showModal("Por favor, preencha todos os campos.");
      return; // Exit the function if any input is empty
    }

    const isValidEmail = await isEmailValid(
      emailInput.value.trim(),
      currentEmail
    );

    if (!isValidEmail) {
      showModal("Este e-mail já está a ser utilizado.");
      return; // Exit the function if email is not valid
    }

    // If all checks pass, allow form submission
    editProfileForm.submit();
  });
};

const isEmailValid = async (newEmail, currentEmail) => {
  if (newEmail == currentEmail) return true;

  const isRegisteredEmail = await isEmailAlreadyRegistered(newEmail);
  if (isRegisteredEmail) return false;

  return true;
};
