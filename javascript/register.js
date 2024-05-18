document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("register-form");
  const emailInput = registerForm.querySelector('input[name="email"]');
  const passwordInput = registerForm.querySelector('input[name="password"]');
  const confirmPasswordInput = registerForm.querySelector(
    'input[name="confirmPassword"]'
  );

  const inputs = registerForm.querySelectorAll("input");

  registerForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const formData = new FormData(registerForm);

    if (emailInput.value == "") {
      showModal("Por favor, insira um email.");
      return;
    }

    const isRegisteredEmail = await isEmailAlreadyRegistered(emailInput.value);

    if (isRegisteredEmail) {
      showModal("Este email já está em uso.");
      return;
    }

    if (passwordInput.value == "") {
      showModal("Por favor, insira uma palavra-passe válida.");
      return;
    }

    if (passwordInput.value !== confirmPasswordInput.value) {
      showModal(
        "As palavras-passe não correspondem. Por favor, tente novamente."
      );
      return;
    }

    let isEmpty = false;

    inputs.forEach((input) => {
      if (input.value.trim() === "") {
        isEmpty = true;
      }
    });

    if (isEmpty) {
      showModal("Por favor, preencha todos os campos.");
      return;
    }

    const formDataObject = Object.fromEntries(formData.entries());

    const response = await fetch("../api/user/index.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formDataObject),
    });

    if (!response.ok) {
      showModal("Ocorreu um erro inesperado. Tente novamente mais tarde.");
    } else {
      window.location.href = redirectURL;
    }
  });
});
