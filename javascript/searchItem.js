document.addEventListener("DOMContentLoaded", () => {
  const queryString = window.location.search;

  // Creating a URLSearchParams object with the query string
  const params = new URLSearchParams(queryString);

  const page = params.get("page");
  const search = params.get("search[search]");
  const location = params.get("search[location]");
  const order = params.get("search[order]");
  const priceFrom = params.get("search[price:from]");
  const priceTo = params.get("search[price:to]");
  const category = params.get("search[category]");
  const attributes = {};
  params.forEach((value, key) => {
    if (key.startsWith("search[attributes]")) {
      const match = key.match(/\[(\d+)\]/);
      if (match) {
        const attributeNumber = match[1];
        attributes[attributeNumber] = value;
      }
    }
  });

  console.log("page:", page);
  console.log("search:", search);
  console.log("location:", location);
  console.log("order:", order);
  console.log("price:", { from: priceFrom, to: priceTo });
  console.log("category:", category);
  console.log("attributes:", attributes);

  // Initial call to handle pagination
  // totalItems = getTotalItems();
  handlePagination(page);
});

const handlePagination = (page) => {
  const itemsPerPage = 10; // Number of items to display per page

  // Simulated data for demonstration
  const totalItems = 100; // Total number of items
  const totalPages = Math.ceil(totalItems / itemsPerPage);

  var currentPage = parseInt(page) || 1;
  if (currentPage > totalPages) currentPage = totalPages;
  if (currentPage < 1) currentPage = 1;

  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;

  // Simulated items for demonstration
  const items = Array.from({ length: totalItems }, (_, i) => `Item ${i + 1}`);
  /*
  const itemsPerPage = 10;
  const offset = (currentPage - 1) * itemsPerPage;
  const items = getItems(itemsPerPage, offset);
  */

  // Display items for the current page
  const itemsContainer = document.getElementById("items-container");
  itemsContainer.innerHTML = ""; // Clear previous items
  for (let i = startIndex; i < Math.min(endIndex, totalItems); i++) {
    const listItem = document.createElement("li");
    listItem.textContent = items[i];
    itemsContainer.appendChild(listItem);
  }

  // Display pagination buttons
  const paginationNav = document.querySelector("#items > nav");
  paginationNav.innerHTML = ""; // Clear previous pagination

  // Add back button if not on the first page
  if (currentPage > 1) createPreviousPageButton(currentPage, paginationNav);

  if (currentPage > 1) {
    createPageButton(1, currentPage, paginationNav);
    if (currentPage - 1 > 1) createPageEllipsis(paginationNav);
  }

  createPageButton(currentPage, currentPage, paginationNav);

  if (currentPage < totalPages) {
    if (totalPages - currentPage > 1) createPageEllipsis(paginationNav);
    createPageButton(totalPages, currentPage, paginationNav);
  }

  if (currentPage < totalPages)
    createNextPageButton(currentPage, paginationNav);
};

const createButton = (text) => {
  const button = document.createElement("button");
  button.textContent = text;
  return button;
};

const createIconButton = (iconName) => {
  const button = document.createElement("button");
  const icon = document.createElement("ion-icon");
  icon.setAttribute("name", iconName);
  button.appendChild(icon);
  return button;
};

const navigateToPage = (page) => {
  const url = new URL(window.location.href);
  url.searchParams.set("page", page);
  history.pushState({}, "", url.toString());
  handlePagination(page);
};

const createPreviousPageButton = (currentPage, paginationNav) => {
  const backButton = createIconButton("chevron-back");
  backButton.setAttribute("id", "previous-page");
  backButton.addEventListener("click", () => {
    navigateToPage(currentPage - 1);
  });
  paginationNav.appendChild(backButton);
};

const createNextPageButton = (currentPage, paginationNav) => {
  const forwardButton = createIconButton("chevron-forward");
  forwardButton.setAttribute("id", "next-page");
  forwardButton.addEventListener("click", () => {
    navigateToPage(currentPage + 1);
  });
  paginationNav.appendChild(forwardButton);
};

const createPageButton = (pageNumber, currentPage, paginationNav) => {
  const pageButton = createButton(pageNumber.toString());
  if (pageNumber === currentPage) {
    pageButton.classList.add("selected-page");
  }
  pageButton.addEventListener("click", () => {
    navigateToPage(pageNumber);
  });
  paginationNav.appendChild(pageButton);
};

const createPageEllipsis = (paginationNav) => {
  const p = document.createElement("p");
  p.textContent = " ... ";
  paginationNav.appendChild(p);
};

// localhost:8000/pages/items.php?page=2&search[search]=item_name&search[location]=location_name&search[order]=created_at:desc&search[price:from]=10&search[price:to]=20&search[category]=category_id&search[attributes][2]=value2&search[attributes][5]=value5
// localhost:8000/api/item/index.php?page=2&search[search]=item_name&search[location]=location_name&search[order]=created_at:desc&search[price:from]=10&search[price:to]=20&search[category]=category_id&search[attributes][2]=value2&search[attributes][5][from]=from5&search[attributes][5][to]=to5
// localhost:8000/api/item/index.php?page=2&search[search]=item_name&search[location]=location_name&search[order]=created_at:desc&search[price][from]=10&search[price][to]=20&search[category]=category_id&search[attributes][2]=value2&search[attributes][5][from]=from5&search[attributes][5][to]=to5

// localhost:8000/pages/items.php?
// page=2&
// search[search]=item_name&
// search[location]=location_name&
// search[order]=created_at:desc&
// search[price:from]=10&search[price:to]=20&
// search[category]=category_id&
// search[attributes][2]=value2&
// search[attributes][5]=value5
