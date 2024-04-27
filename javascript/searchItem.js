let itemsTotal;
let currentPage;
const itemsPerPage = 10;

document.addEventListener("DOMContentLoaded", async () => {
  const queryString = window.location.search;
  const params = new URLSearchParams(queryString);

  currentPage = params.get("page");
  if (!currentPage || isNaN(parseInt(currentPage))) currentPage = 1;

  handleOrderSelector();
  searchItems();
});

const searchItems = async () => {
  itemsTotal = await getItemsTotal();
  handlePagination();
};

const handleOrderSelector = () => {
  const selectElement = document.getElementById("items-order");

  selectElement.addEventListener("change", (event) => {
    const selectedValue = event.target.value;
    const url = new URL(window.location.href);
    url.searchParams.set("search[order]", selectedValue);
    history.pushState({}, "", url.toString());
    searchItems();
  });
};

const getItemsTotal = async () => {
  return fetch(`./../api/item/index.php?total=1${window.location.search}`, {
    method: "GET",
  })
    .then((response) => {
      if (response.status == 404) {
        return 0;
      } else if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .catch((error) => {
      console.error("There was an unexpected error:", error);
    });
};

const getItems = async () => {
  return fetch(
    `./../api/item/index.php?page=${currentPage}&itemsPerPage=${itemsPerPage}${window.location.search}`,
    {
      method: "GET",
    }
  )
    .then((response) => {
      if (response.status == 404) {
        return [];
      } else if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .catch((error) => {
      console.error("There was an unexpected error:", error);
    });
};

const handlePagination = async () => {
  const totalPages = Math.ceil(itemsTotal / itemsPerPage);

  currentPage = parseInt(currentPage) || 1;
  if (currentPage > totalPages) currentPage = totalPages;
  if (currentPage < 1) currentPage = 1;

  const items = await getItems();

  renderItemsTotal();
  renderItems(items);
  renderNavButtons(totalPages);
};

const renderItemsTotal = () => {
  const h2ItemsTotal = document.querySelector("#items > header > h2");
  if (itemsTotal > 1000)
    h2ItemsTotal.textContent = `Encontrámos mais de ${1000} anúncios`;
  else h2ItemsTotal.textContent = `Encontrámos ${itemsTotal} anúncios`;
};

const renderNavButtons = (totalPages) => {
  const paginationNav = document.querySelector("#items > nav");
  paginationNav.innerHTML = "";

  if (totalPages <= 1) return;

  if (currentPage > 1) createPreviousPageButton(paginationNav);

  if (currentPage > 1) {
    createPageButton(1, paginationNav);
    if (currentPage - 1 > 1) createPageEllipsis(paginationNav);
  }

  createPageButton(currentPage, paginationNav);

  if (currentPage < totalPages) {
    if (totalPages - currentPage > 1) createPageEllipsis(paginationNav);
    createPageButton(totalPages, paginationNav);
  }

  if (currentPage < totalPages) createNextPageButton(paginationNav);
};

const renderItems = (items) => {
  const itemsContainer = document.getElementById("items-container");
  itemsContainer.innerHTML = "";

  if (itemsTotal == 0) {
    const li = document.createElement("li");
    const h2 = document.createElement("h2");
    h2.textContent = "Não encontramos resultados!";
    h2.style.textAlign = "center";
    h2.style.marginTop = "10rem";
    h2.style.marginBottom = "10rem";
    li.appendChild(h2);
    li.style.justifyContent = "center";
    li.style.alignItems = "center";
    li.style.height = "auto";
    itemsContainer.appendChild(li);
    return;
  }

  for (const key in items) {
    if (items.hasOwnProperty(key)) {
      const itemData = items[key];
      const image =
        itemData.item.images && itemData.item.images[0]
          ? itemData.item.images[0].path
          : null;

      const li = document.createElement("li");
      let img;
      if (image) img = document.createElement("img");
      else img = document.createElement("h3");
      const imgDiv = document.createElement("div");
      const div1 = document.createElement("div");
      const div2 = document.createElement("div");
      const div3 = document.createElement("div");
      const div4 = document.createElement("div");
      const div5 = document.createElement("div");
      const div6 = document.createElement("div");
      const h3Name = document.createElement("h3");
      const h3Price = document.createElement("h3");
      const h4City = document.createElement("h4");
      const pStateCountry = document.createElement("p");
      const p = document.createElement("p");
      const buttonCart = document.createElement("button");
      const buttonWishlist = document.createElement("button");
      const iconCart = document.createElement("ion-icon");
      const iconWishlist = document.createElement("ion-icon");

      if (image) {
        img.src = `${window.location.protocol}//${window.location.host}/${image}`;
        img.alt = "Item Image";
      } else {
        img.textContent = "Este anúncio não possui imagens.";
        img.style.fontWeight = "600";
        img.style.alignSelf = "center";
        img.style.textAlign = "center";
      }

      h3Name.textContent = itemData.item.name;
      h3Price.textContent = `${itemData.item.price} €`;
      p.textContent = "Negociável";
      h4City.textContent = `${itemData.seller.city}`;
      pStateCountry.textContent = `${itemData.seller.state}, ${itemData.seller.country}`;
      iconCart.name = itemData.in_cart ? "cart" : "cart-outline";
      iconWishlist.name = itemData.in_wishlist ? "heart" : "heart-outline";
      imgDiv.appendChild(img);
      li.appendChild(imgDiv);
      div1.appendChild(div2);
      div2.appendChild(h3Name);
      div2.appendChild(div3);
      div3.appendChild(h3Price);
      div3.appendChild(p);
      div1.appendChild(div4);
      div4.appendChild(div5);
      div4.appendChild(div6);
      div5.appendChild(h4City);
      div5.appendChild(pStateCountry);
      div6.appendChild(buttonCart);
      div6.appendChild(buttonWishlist);
      buttonCart.appendChild(iconCart);
      buttonWishlist.appendChild(iconWishlist);
      li.appendChild(div1);
      itemsContainer.appendChild(li);
    }
  }
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
  currentPage = page;
  history.pushState({}, "", url.toString());
  handlePagination();
  const itemsElement = document.getElementById("items");
  if (itemsElement) {
    itemsElement.scrollIntoView();
  }
};

const createPreviousPageButton = (paginationNav) => {
  const backButton = createIconButton("chevron-back");
  backButton.setAttribute("id", "previous-page");
  backButton.addEventListener("click", () => {
    navigateToPage(currentPage - 1);
  });
  paginationNav.appendChild(backButton);
};

const createNextPageButton = (paginationNav) => {
  const forwardButton = createIconButton("chevron-forward");
  forwardButton.setAttribute("id", "next-page");
  forwardButton.addEventListener("click", () => {
    navigateToPage(currentPage + 1);
  });
  paginationNav.appendChild(forwardButton);
};

const createPageButton = (pageNumber, paginationNav) => {
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
