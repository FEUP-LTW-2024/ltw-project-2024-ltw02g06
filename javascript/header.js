document.addEventListener("DOMContentLoaded", () => {
  const hamburgerMenu = document.getElementById("hamburger-menu");
  const nav = document.querySelector("nav");

  hamburgerMenu.addEventListener("click", () => {
    nav.classList.toggle("show");
    const icon = hamburgerMenu.querySelector("ion-icon");
    if (nav.classList.contains("show")) {
      icon.name = "close-outline";
      document.body.style.overflow = "hidden";
    } else {
      icon.name = "reorder-four-outline";
      document.body.style.overflow = "";
    }
  });
});
