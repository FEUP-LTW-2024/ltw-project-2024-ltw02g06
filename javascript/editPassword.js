document.addEventListener("DOMContentLoaded", () => {
  const editPasswordForm = document.getElementById("edit-password");
  const newPasswordInput = editPasswordForm.querySelector(
    'input[name="newPassword"]'
  );
  const confirmNewPasswordInput = editPasswordForm.querySelector(
    'input[name="confirmNewPassword"]'
  );

  const editPasswordCancelBtn = document.getElementById(
    "edit-password-cancel-btn"
  );
  editPasswordCancelBtn.addEventListener("click", () => {
    window.location.href = "../pages/profile.php";
  });

  editPasswordForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (newPasswordInput.value == "") {
      showModal("Por favor, insira uma palavra-passe válida.");
      return;
    }

    if (newPasswordInput.value !== confirmNewPasswordInput.value) {
      showModal(
        "As palavras-passe não correspondem. Por favor, tente novamente."
      );
      return;
    }

    const formData = new FormData(e.target);
    const formDataObject = Object.fromEntries(formData.entries());

    const response = await fetch("../api/user/auth.php", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formDataObject),
    });
    if (!response.ok) {
      let messsage = response.message;
      if (response.status == 401) {
        messsage = "Credenciais inválidas!";
      } else if (response.status == 400) {
        messsage = "Credenciais inválidas!";
      } else {
        messsage =
          "Ocorreu um problema inesperado! Por favor, tente novamente mais tarde.";
      }
      showModal(messsage);
    } else {
      window.location.href = "../pages/profile.php";
    }
  });
});
