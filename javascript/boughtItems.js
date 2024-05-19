document.addEventListener("DOMContentLoaded", async () => {
  const boughtItems = document.querySelectorAll("#items-container li");

  boughtItems.forEach((item) => {
    handleBoughtItem(item);
  });
});

const handleBoughtItem = (item) => {
  const itemId = item.closest("li").dataset.itemId;
  item.addEventListener("click", async () => {
    window.location.href = `../pages/item.php?id=${itemId}`;
  });
};
