let itemsTotal;
let currentPage;
const itemsPerPage = 10;
let categories;

document.addEventListener("DOMContentLoaded", () => {
  const queryString = window.location.search;
  const params = new URLSearchParams(queryString);

  currentPage = params.get("page");
  if (!currentPage || isNaN(parseInt(currentPage))) currentPage = 1;

  handleSearchBar();
  handleOrderSelector();
  handleFiltersList();
  searchItems();
});

const searchItems = async () => {
  itemsTotal = await getItemsTotal();
  handlePagination();
};

const handleSearchBar = () => {
  const queryString = window.location.search;
  const params = new URLSearchParams(queryString);

  let currentSearchName = params.get("search[search]");
  if (!currentSearchName || isNaN(currentSearchName)) {
    currentSearchName = null;
    deleteParam("search[search]");
  }

  let currentSearchLocation = params.get("search[location]");
  if (!currentSearchLocation || isNaN(currentSearchLocation)) {
    currentSearchLocation = null;
    deleteParam("search[location]");
  }

  const searchNameInput = document.querySelector("#search-bar > input");
  const searchLocationInput = document.querySelector(
    "#search-location > input"
  );

  searchNameInput.value = currentSearchName;
  searchNameInput.addEventListener("input", () => {
    if (searchNameInput.value == "") deleteParam(`search[search]`);
    else updateParam(`search[search]`, searchNameInput.value);
    searchItems();
  });

  searchLocationInput.value = currentSearchLocation;
  searchLocationInput.addEventListener("input", () => {
    if (searchLocationInput.value == "") deleteParam(`search[location]`);
    else updateParam(`search[location]`, searchLocationInput.value);
    searchItems();
  });
};

const handleOrderSelector = () => {
  const selectElement = document.getElementById("items-order");

  selectElement.addEventListener("change", (event) => {
    const selectedValue = event.target.value;
    updateParam("search[order]", selectedValue);
    searchItems();
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

const handleFiltersList = () => {
  const queryString = window.location.search;
  const params = new URLSearchParams(queryString);

  let currentCategoryId = params.get("search[category]");
  if (!currentCategoryId || isNaN(parseInt(currentCategoryId))) {
    currentCategoryId = null;
    deleteParam("search[category]");
  }

  let currentPriceFrom = params.get("search[price][from]");
  if (!currentPriceFrom || isNaN(parseFloat(currentPriceFrom))) {
    currentPriceFrom = null;
    deleteParam("search[price][from]");
  }

  let currentPriceTo = params.get("search[price][to]");
  if (!currentPriceTo || isNaN(parseFloat(currentPriceTo))) {
    currentPriceTo = null;
    deleteParam("search[price][to]");
  }

  const attributes = {};

  for (const [key, value] of params.entries()) {
    if (key.startsWith("search[attributes][")) {
      const match = key.match(/\[(\d+)\](\[\w+\])?/);
      if (match) {
        const attributeId = match[1];
        if (match[2] === "[from]") {
          // Handle from parameter
          attributes[attributeId].from = value;
        } else if (match[2] === "[to]") {
          attributes[attributeId].to = value;
        } else {
          attributes[attributeId] = value;
        }
      }
    }
  }

  const categorySelect = document.getElementById("category");
  const priceRange = document.getElementById("price-range");

  const priceFrom = priceRange.querySelector(
    "input[name=\"search['price']['from']\"]"
  );
  const priceTo = priceRange.querySelector(
    "input[name=\"search['price']['to']\"]"
  );
  initializePriceRangeInput(priceFrom, currentPriceFrom, "search[price][from]");
  initializePriceRangeInput(priceTo, currentPriceTo, "search[price][to]");

  const filtersList = document.getElementById("filters-list");

  const updateAttributes = () => {
    const selectedCategory = categories[currentCategoryId];

    while (filtersList.childNodes.length > 5) {
      filtersList.removeChild(filtersList.lastChild);
    }

    if (!currentCategoryId || !selectedCategory) {
      deleteParam("search[category]");
      categorySelect.value = "all";
      return;
    }

    if (!currentPriceFrom) {
      deleteParam("search[price][from]");
      priceFrom.textContent = "";
    }

    if (!currentPriceTo) {
      deleteParam("search[price][to]");
      priceTo.textContent = "";
    }

    // Add new attributes
    for (const attributeId in selectedCategory.attributes) {
      const attribute = selectedCategory.attributes[attributeId];
      const listItem = document.createElement("li");
      const label = document.createElement("label");
      label.setAttribute("for", attribute.id);
      label.textContent = attribute.name;
      listItem.appendChild(label);

      let inputElement;

      if (attribute.type === "default") {
        inputElement = document.createElement("input");
        inputElement.setAttribute("type", "text");
        inputElement.setAttribute("placeholder", attribute.name);
        inputElement.value = attributes[attributeId]
          ? attributes[attributeId]
          : "";
        inputElement.addEventListener("input", () => {
          if (inputElement.value == "")
            deleteParam(`search[attributes][${attributeId}]`);
          else
            updateParam(
              `search[attributes][${attributeId}]`,
              inputElement.value
            );
          searchItems();
        });
      } else if (attribute.type === "int" || attribute.type === "real") {
        // TODO check this code:
        inputElement = document.createElement("div");
        inputElement.setAttribute("class", "range-filter");

        inputFrom = document.createElement("input");
        inputFrom.setAttribute("type", "text");
        inputFrom.setAttribute("placeholder", "De");
        inputFrom.setAttribute("name", `attributes[${attributeId}][from]`);
        // inputFrom.value = attributes[attributeId]?.from || "";

        if (attribute.type === "int")
          initializeIntInput(
            inputFrom,
            attributes[attributeId]?.from || "",
            `search[attributes][${attributeId}][from]`
          );
        else
          initializeFloatInput(
            inputFrom,
            attributes[attributeId]?.from || "",
            `search[attributes][${attributeId}][from]`
          );

        inputTo = document.createElement("input");
        inputTo.setAttribute("type", "text");
        inputTo.setAttribute("placeholder", "Até");
        inputTo.setAttribute("name", `attributes[${attributeId}][to]`);
        // inputTo.value = attributes[attributeId]?.to || "";

        if (attribute.type === "int")
          initializeIntInput(
            inputTo,
            attributes[attributeId]?.to || "",
            `search[attributes][${attributeId}][to]`
          );
        else
          initializeFloatInput(
            inputTo,
            attributes[attributeId]?.to || "",
            `search[attributes][${attributeId}][to]`
          );

        inputElement.appendChild(inputFrom);
        inputElement.appendChild(inputTo);
      } else if (attribute.type === "enum") {
        inputElement = document.createElement("select");

        const showAllItemsOption = document.createElement("option");
        showAllItemsOption.value = "all";
        showAllItemsOption.textContent = "Mostrar Tudo";
        inputElement.appendChild(showAllItemsOption);

        attribute.values.forEach((value) => {
          const option = document.createElement("option");
          option.value = value.value;
          option.textContent = value.value;
          inputElement.appendChild(option);
        });

        inputElement.value = attributes[attributeId]
          ? attributes[attributeId]
          : "all";

        inputElement.addEventListener("change", () => {
          updateParam(`search[attributes][${attributeId}]`, inputElement.value);
          searchItems();
        });
      }

      inputElement.setAttribute("name", `attributes[${attributeId}]`);
      inputElement.setAttribute("id", attribute.name);

      listItem.appendChild(inputElement);
      filtersList.appendChild(listItem);
    }
  };

  // Event listener for category select change
  categorySelect.addEventListener("change", () => {
    currentCategoryId = categorySelect.value;
    updateParam("search[category]", currentCategoryId);
    deleteAllAtributes();
    const selectedCategory = categories[currentCategoryId];
    if (!currentCategoryId || !selectedCategory) {
      deleteParam("search[category]");
      categorySelect.value = "all";
    }
    searchItems();
    updateAttributes();
  });

  updateAttributes();
};

const initializePriceRangeInput = (inputElement, currentPrice, param) => {
  inputElement.value = currentPrice;
  validatePriceInput(inputElement);

  if (!isNaN(inputElement.value) && inputElement.value != "")
    updateParam(param, inputElement.value);
  else deleteParam(param);

  inputElement.addEventListener("input", () => {
    validatePriceInput(inputElement);
  });

  inputElement.addEventListener("change", () => {
    const value = parseFloat(inputElement.value);
    if (!isNaN(value)) updateParam(param, value);
    else deleteParam(param);
    searchItems();
  });
};

// TODO test this code:
const initializeFloatInput = (inputElement, currentValue, param) => {
  inputElement.value = currentValue;
  validateFloatInput(inputElement);

  if (!isNaN(inputElement.value) && inputElement.value != "")
    updateParam(param, inputElement.value);
  else deleteParam(param);

  inputElement.addEventListener("input", () => {
    validateFloatInput(inputElement);
  });

  inputElement.addEventListener("change", () => {
    const value = parseFloat(inputElement.value);
    if (!isNaN(value)) updateParam(param, value);
    else deleteParam(param);
    searchItems();
  });
};

// TODO test this code:
const initializeIntInput = (inputElement, currentValue, param) => {
  inputElement.value = currentValue;
  validateIntInput(inputElement);

  if (!isNaN(inputElement.value) && inputElement.value != "")
    updateParam(param, inputElement.value);
  else deleteParam(param);

  inputElement.addEventListener("input", () => {
    validateIntInput(inputElement);
  });

  inputElement.addEventListener("change", () => {
    const value = parseInt(inputElement.value);
    if (!isNaN(value)) updateParam(param, value);
    else deleteParam(param);
    searchItems();
  });
};

const validatePriceInput = (inputElement) => {
  var inputValue = inputElement.value;
  inputValue = inputValue.replace(/[^\d.]/g, "");

  var dotIndex = inputValue.indexOf(".");
  if (dotIndex !== -1)
    inputValue =
      inputValue.substr(0, dotIndex + 1) +
      inputValue.substr(dotIndex + 1).replace(/\./g, "");

  inputValue = inputValue.replace(/^0+(?=\d)/, "");
  var decimalRegex = /^\d*\.?\d{0,2}$/;
  if (!decimalRegex.test(inputValue)) inputValue = "0";

  inputElement.value = inputValue;
};

const validateFloatInput = (inputElement) => {
  var inputValue = inputElement.value;
  inputValue = inputValue.replace(/[^\d.-]/g, "");

  var dotIndex = inputValue.indexOf(".");
  if (dotIndex !== -1)
    inputValue =
      inputValue.substr(0, dotIndex + 1) +
      inputValue.substr(dotIndex + 1).replace(/\./g, "");

  inputValue = inputValue.replace(/^(-)?0+(?=\d)/, "$1");

  var decimalRegex = /^-?\d*\.?\d{0,2}$/;
  if (!decimalRegex.test(inputValue)) inputValue = "0";

  inputElement.value = inputValue;
};

const validateIntInput = (inputElement) => {
  var inputValue = inputElement.value;
  inputValue = inputValue.replace(/[^\d-]/g, "");
  inputValue = inputValue.replace(/^(-)?0+(?=\d)/, "$1");

  var integerRegex = /^-?\d*$/;
  if (!integerRegex.test(inputValue)) inputValue = "0";

  inputElement.value = inputValue;
};

const updateParam = (param, value) => {
  const url = new URL(window.location.href);
  url.searchParams.set(param, value);
  history.pushState({}, "", url.toString());
};

const deleteParam = (param) => {
  const url = new URL(window.location.href);
  url.searchParams.delete(param);
  history.pushState({}, "", url.toString());
};

const deleteAllAtributes = () => {
  const url = new URL(window.location.href);
  const paramsKeys = Array.from(url.searchParams.keys());
  const filteredKeys = paramsKeys.filter((key) =>
    key.startsWith("search[attributes]")
  );
  filteredKeys.forEach((key) => {
    url.searchParams.delete(key);
  });
  history.pushState({}, "", url.toString());
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

const renderItemsTotal = () => {
  const h2ItemsTotal = document.querySelector("#items > header > h2");
  if (itemsTotal > 1000)
    h2ItemsTotal.textContent = `Encontrámos mais de ${1000} anúncios`;
  else h2ItemsTotal.textContent = `Encontrámos ${itemsTotal} anúncios`;
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
      const li = renderItem(itemData);
      itemsContainer.appendChild(li);
    }
  }
};

// TODO make wishlist/cart button wok
const renderItem = (itemData) => {
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
  // div4.appendChild(div6);
  div5.appendChild(h4City);
  div5.appendChild(pStateCountry);
  div6.appendChild(buttonCart);
  div6.appendChild(buttonWishlist);
  buttonCart.appendChild(iconCart);
  buttonWishlist.appendChild(iconWishlist);
  li.appendChild(div1);

  return li;
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
  currentPage = page;
  updateParam("page", page);
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
