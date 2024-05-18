document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("login-form");
  const emailInput = loginForm.querySelector('input[name="email"]');
  const passwordInput = loginForm.querySelector('input[name="password"]');

  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const response = await fetch("../api/user/auth.php", {
      method: "POST",
      body: formData,
    });
    if (!response.ok) {
      emailInput.value = "";
      passwordInput.value = "";
      showModal("Credenciais inválidas!");
    } else {
      window.location.href = redirectURL;
    }
  });
});
