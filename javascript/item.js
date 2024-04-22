document.addEventListener("DOMContentLoaded", function () {
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
