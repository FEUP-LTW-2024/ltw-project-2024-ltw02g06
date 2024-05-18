let itemsTotal;

document.addEventListener("DOMContentLoaded", () => {
  const queryString = window.location.search;
  const params = new URLSearchParams(queryString);

  currentPage = params.get("page");
  if (!currentPage || isNaN(parseInt(currentPage))) currentPage = 1;

  handleSearchBar();
  handleOrderSelector();
  searchItems();
});

const searchItems = async () => {
  itemsTotal = await getItemsTotal();
  const items = await getItems();
  renderItems(items);
};

// TODO clean up this code:
const handleSearchBar = () => {
  const queryString = window.location.search;
  const params = new URLSearchParams(queryString);

  let currentSearchName = params.get("search[search]");
  if (!currentSearchName || isNaN(currentSearchName)) {
    currentSearchName = null;
    deleteParam("search[search]");
  }

  const searchNameInput = document.querySelector("#small-search-bar > input");
  searchNameInput.value = currentSearchName;
  searchNameInput.addEventListener("input", () => {
    if (searchNameInput.value == "") deleteParam(`search[search]`);
    else updateParam(`search[search]`, searchNameInput.value);
    searchItems();
  });

  const searchBtn = document.querySelector(
    "#small-search-bar-container > button"
  );
  searchBtn.addEventListener("click", searchItems);
};

const handleOrderSelector = () => {
  const selectElement = document.getElementById("items-order");

  selectElement.addEventListener("change", (event) => {
    const selectedValue = event.target.value;
    updateParam("search[order]", selectedValue);
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

const getItemsTotal = async () => {
  return fetch(
    `./../api/item/index.php?${window.location.search}&total=1&user=${userId}&status=all`,
    {
      method: "GET",
    }
  )
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
    `./../api/item/index.php?${
      window.location.search
    }&page=${1}&itemsPerPage=${itemsTotal}&user=${userId}&status=all`,
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

const sendItem = async (itemId) => {
  fetch(`./../api/item/send.php?&id=${itemId}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ csrf: csrf }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
    })
    .catch((error) => {
      console.error("There was an unexpected error:", error);
    });
};

const renderItems = (items) => {
  const activeItemsList = document.getElementById("active-items-list");
  const toSendItemsList = document.getElementById("to-send-items-list");
  const soldItemsList = document.getElementById("sold-items-list");

  activeItemsList.innerHTML = "";
  toSendItemsList.innerHTML = "";
  soldItemsList.innerHTML = "";

  for (const key in items) {
    if (items.hasOwnProperty(key)) {
      const itemData = items[key];
      if (itemData.item.status == "active") {
        const li = renderActiveItem(itemData);
        activeItemsList.appendChild(li);
      } else if (itemData.item.status == "to send") {
        const li = renderToSendItem(itemData);
        toSendItemsList.appendChild(li);
      } else if (itemData.item.status == "sold") {
        const li = renderSoldItem(itemData);
        soldItemsList.appendChild(li);
      }
    }
  }

  renderNoItemsFound(activeItemsList);
  renderNoItemsFound(toSendItemsList);
  renderNoItemsFound(soldItemsList);
};

const renderNoItemsFound = (element) => {
  if (element.innerHTML == "") {
    const li = document.createElement("li");
    const h3 = document.createElement("h3");
    h3.textContent = "Não encontramos resultados!";
    h3.style.textAlign = "center";
    h3.style.marginTop = "5rem";
    h3.style.marginBottom = "5rem";
    li.appendChild(h3);
    li.style.justifyContent = "center";
    li.style.alignItems = "center";
    li.style.height = "auto";
    element.style.overflowY = "none";
    li.className = "no-items-found";
    element.appendChild(li);
  }
};

const renderActiveItem = (itemData) => {
  const li = document.createElement("li");
  const div1 = document.createElement("div");
  div1.style.cursor = "pointer";
  div1.addEventListener("click", () => {
    window.location.href = `/pages/item.php?id=${itemData.item.id}`;
  });
  const div2 = document.createElement("div");
  const h3Name = document.createElement("h3");
  const h3Price = document.createElement("h3");
  const promoteButton = document.createElement("button");
  const editButton = document.createElement("button");
  const promoteIcon = document.createElement("ion-icon");
  const editIcon = document.createElement("ion-icon");

  editButton.addEventListener("click", () => {
    window.location.href =
      `/pages/item.edit.php?id=${itemData.item.id}&redirect=` +
      encodeURIComponent(`/pages/seller.php${window.location.search}`);
  });

  promoteIcon.addEventListener("click", () => {});

  h3Name.textContent = itemData.item.name;
  h3Price.textContent = `${itemData.item.price} €`;
  promoteIcon.name = "star-outline";
  editIcon.name = "create-outline";
  div1.appendChild(h3Name);
  div1.appendChild(h3Price);
  // div2.appendChild(promoteButton);
  div2.appendChild(editButton);
  promoteButton.appendChild(promoteIcon);
  promoteButton.title = "Promover";
  editButton.appendChild(editIcon);
  editButton.title = "Editar";
  li.appendChild(div1);
  li.appendChild(div2);

  return li;
};

const renderToSendItem = (itemData) => {
  const li = document.createElement("li");
  const div1 = document.createElement("div");
  div1.style.cursor = "pointer";
  div1.addEventListener("click", () => {
    window.location.href = `/pages/item.php?id=${itemData.item.id}`;
  });

  const div2 = document.createElement("div");
  const h3Name = document.createElement("h3");
  const h3Price = document.createElement("h3");
  const sendButton = document.createElement("button");
  const sendIcon = document.createElement("ion-icon");

  sendButton.addEventListener("click", async () => {
    await sendItem(itemData["item"].id);
    renderSoldItem(itemData);
    sendButton.parentElement.delete;
  });

  sendButton.addEventListener("click", async () => {
    try {
      var htmlContent = `
        <html>
            <head>
                <title>My HTML File</title>
                <style>
                    body {
                      font-family: "Montserrat", sans-serif;
                    }
                    h2:last-of-type() {
                        padding-top: 2rem
                    }
                </style>
            </head>
            <body>
              <h2><strong>Remetente:</strong> </h2>
              <p><strong>Nome:</strong> ${itemData["seller"].first_name} ${itemData["seller"].last_name}</p>
              <p><strong>Email:</strong> ${itemData["seller"].email}</p>
              <p><strong>Endereço:</strong> ${itemData["seller"].address}, ${itemData["seller"].city}, ${itemData["seller"].state}, ${itemData["seller"].country} - ${itemData["seller"].zipcode}</p>
              <p><strong>Artigo:</strong> #${itemData["item"].id}</p>
              
              <h2><strong>Destinatário:</strong> </h2>
              <p><strong>Nome:</strong> ${itemData["buyer"].first_name} ${itemData["buyer"].last_name}</p>
              <p><strong>Email:</strong> ${itemData["buyer"].email}</p>
              <p><strong>Endereço:</strong> ${itemData["buyer"].address}, ${itemData["buyer"].city}, ${itemData["buyer"].state}, ${itemData["buyer"].country} - ${itemData["buyer"].zipcode}</p>            
            </body>
        </html>`;

      var doc = new jsPDF();
      doc.fromHTML(htmlContent, 15, 15, {
        width: 170,
      });
      doc.save(`eKo_shipping_form-item_${itemData["item"].id}.pdf`);

      await sendItem(itemData.item.id);
      const toSendItemsList = document.getElementById("to-send-items-list");
      const soldItemsList = document.getElementById("sold-items-list");
      const noItemsFoundLi = document.querySelector(
        "#sold-items-list .no-items-found"
      );
      if (noItemsFoundLi) soldItemsList.innerHTML = "";
      soldItemsList.appendChild(renderSoldItem(itemData));
      decreaseNumberToSentItems();
      increaseNumberSoldItems();
      increaseRevenue(itemData["item"].sold_price);
      li.remove();
      renderNoItemsFound(toSendItemsList);
    } catch (error) {
      console.error("Error sending item:", error);
    }
  });

  h3Name.textContent = itemData.item.name;
  h3Price.textContent = `${itemData.item.sold_price} €`;
  h3Price.innerHTML =
    itemData.item.sold_price == itemData.item.price
      ? `${itemData.item.sold_price} €`
      : `${itemData.item.sold_price} € <span>${itemData.item.price}</span>`;
  sendIcon.name = "send-outline";
  div1.appendChild(h3Name);
  div1.appendChild(h3Price);
  div2.appendChild(sendButton);
  sendButton.appendChild(sendIcon);
  sendButton.title = "Enviar";
  li.appendChild(div1);
  li.appendChild(div2);

  return li;
};

const renderSoldItem = (itemData) => {
  const li = document.createElement("li");
  const div1 = document.createElement("div");
  div1.style.cursor = "pointer";
  div1.addEventListener("click", () => {
    window.location.href = `/pages/item.php?id=${itemData.item.id}`;
  });
  const div2 = document.createElement("div");
  const h3Name = document.createElement("h3");
  const h3Price = document.createElement("h3");
  const deleteButton = document.createElement("button");
  const deleteIcon = document.createElement("ion-icon");

  h3Name.textContent = itemData.item.name;
  h3Price.textContent = `${itemData.item.sold_price} €`;
  deleteIcon.name = "trash-outline";
  div1.appendChild(h3Name);
  div1.appendChild(h3Price);
  // div2.appendChild(deleteButton);
  deleteButton.appendChild(deleteIcon);
  deleteButton.title = "Apagar";
  li.appendChild(div1);
  li.appendChild(div2);

  return li;
};

const increaseRevenue = (value) => {
  const revenue = document.getElementById("revenue");
  revenue.textContent = `${parseFloat(revenue.textContent) + value} €`;
};

const decreaseRevenue = (value) => {
  const revenue = document.getElementById("revenue");
  revenue.textContent = `${parseFloat(revenue.textContent) - value} €`;
};

const increaseNumberToSentItems = () => {
  const toSent = document.getElementById("to-sent");
  toSent.textContent = parseInt(toSent.textContent) + 1;
};

const decreaseNumberToSentItems = () => {
  const toSent = document.getElementById("to-sent");
  toSent.textContent = parseInt(toSent.textContent) - 1;
};

const increaseNumberSoldItems = () => {
  const sold = document.getElementById("sold");
  sold.textContent = parseInt(sold.textContent) + 1;
};

const decreaseNumberSoldItems = () => {
  const sold = document.getElementById("sold");
  sold.textContent = parseInt(sold.textContent) - 1;
};

const increaseNumberActiveItems = () => {
  const active = document.getElementById("active");
  active.textContent = parseInt(active.textContent) + 1;
};

const decreaseNumberActiveItems = () => {
  const active = document.getElementById("active");
  active.textContent = parseInt(active.textContent) - 1;
};
