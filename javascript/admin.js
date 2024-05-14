document.addEventListener("DOMContentLoaded", () => {
  handleCategorySection();
  handleSearchBar();
  searchUsers();
});

const handleCategorySection = async () => {
  const categories = await getCategories();
  renderCategories(categories);
  handleAddCategory();
};

const renderCategories = (categories) => {
  const categoriesList = document.querySelector(
    "#admin-categories-section > ul"
  );

  categoriesList.innerHTML = "";

  for (const key in categories) {
    if (categories.hasOwnProperty(key)) {
      const category = categories[key];
      const li = renderCategory(category);
      categoriesList.appendChild(li);
    }
  }
};

const renderCategory = (category) => {
  const li = document.createElement("li");
  const pName = document.createElement("p");
  const editButton = document.createElement("button");
  const removeButton = document.createElement("button");
  const editIcon = document.createElement("ion-icon");
  const removeIcon = document.createElement("ion-icon");

  editButton.addEventListener("click", async () => {
    window.location.href = `/pages/category.php?id=${category.id}`;
  });

  removeButton.addEventListener("click", async () => {
    try {
      removeCategory(category.id);
      li.remove();
    } catch (error) {
      console.error("Error removing category:", error);
    }
  });

  pName.textContent = category.name;
  editIcon.name = "create-outline";
  removeIcon.name = "trash-outline";
  li.appendChild(pName);
  if (category.id != 1) {
    li.appendChild(editButton);
    li.appendChild(removeButton);
  }
  editButton.appendChild(editIcon);
  editButton.title = "Editar";
  removeButton.appendChild(removeIcon);
  removeButton.title = "Remover";

  return li;
};

const handleAddCategory = () => {
  const addCategoryButton = document.getElementById("add-category-button");

  addCategoryButton.addEventListener("click", async () => {
    const newCategoryInput = document.getElementById("new-category-input");
    const newCategory = newCategoryInput.value.trim();
    if (newCategory !== "") {
      await addNewCategory(newCategory);
      handleCategorySection();
      newCategoryInput.value = "";
    }
  });
};

const addNewCategory = async (newCategory) => {
  fetch(`./../api/category/index.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ name: newCategory, attributes: [] }),
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

const searchUsers = async () => {
  const users = await getUsers();
  renderUsers(users);
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
    if (searchNameInput.value.trim() == "") deleteParam(`search[search]`);
    else updateParam(`search[search]`, searchNameInput.value.trim());
    searchUsers();
  });

  const searchBtn = document.querySelector(
    "#small-search-bar-container > button"
  );
  searchBtn.addEventListener("click", searchUsers);
};

// TODO change api call URL
const getUsers = async () => {
  return fetch(`./../api/user/index.php?${window.location.search}`, {
    method: "GET",
  })
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

const renderUsers = (users) => {
  const adminsList = document.getElementById("admins-list");
  const usersList = document.getElementById("users-list");

  adminsList.innerHTML = "";
  usersList.innerHTML = "";

  for (const key in users) {
    if (users.hasOwnProperty(key)) {
      const user = users[key];
      if (user.admin) {
        const li = renderAdmin(user);
        adminsList.appendChild(li);
      } else {
        const li = renderUser(user);
        usersList.appendChild(li);
      }
    }
  }

  renderNoUsersFound(adminsList);
  renderNoUsersFound(usersList);
};

const renderAdmin = (user) => {
  const li = document.createElement("li");
  const div1 = document.createElement("div");
  div1.style.cursor = "pointer";
  div1.addEventListener("click", () => {
    window.location.href = `/pages/profile.php?user=${user.id}`;
  });
  const div2 = document.createElement("div");
  const h3Name = document.createElement("h3");
  const pId = document.createElement("p");
  const adminButton = document.createElement("button");
  const removeButton = document.createElement("button");
  const adminIcon = document.createElement("ion-icon");
  const removeIcon = document.createElement("ion-icon");

  adminButton.addEventListener("click", async () => {
    try {
      removeAdmin(user.id);
      const adminsList = document.getElementById("admins-list");
      const usersList = document.getElementById("users-list");
      const noItemsFoundLi = document.querySelector(
        "#users-list .no-users-found"
      );
      if (noItemsFoundLi) usersList.innerHTML = "";
      usersList.appendChild(renderUser(user));
      li.remove();
      renderNoUsersFound(adminsList);
    } catch (error) {
      console.error("Error removing admin:", error);
    }
  });

  removeButton.addEventListener("click", async () => {
    try {
      removeUser(user.id);
      const adminsList = document.getElementById("admins-list");
      li.remove();
      renderNoUsersFound(adminsList);
    } catch (error) {
      console.error("Error removing admin:", error);
    }
  });

  h3Name.textContent = `${user.first_name} ${user.last_name}`;
  pId.textContent = `#${user.id}`;
  adminIcon.name = "star-half-outline";
  removeIcon.name = "trash-outline";
  div1.appendChild(h3Name);
  div1.appendChild(pId);
  if (user.id != sessionId) {
    div2.appendChild(adminButton);
    div2.appendChild(removeButton);
  }
  adminButton.appendChild(adminIcon);
  adminButton.title = "Remover cargo";
  removeButton.appendChild(removeIcon);
  removeButton.title = "Apagar utilizador";
  li.appendChild(div1);
  li.appendChild(div2);

  return li;
};

const renderUser = (user) => {
  const li = document.createElement("li");
  const div1 = document.createElement("div");
  div1.style.cursor = "pointer";
  div1.addEventListener("click", () => {
    window.location.href = `/pages/profile.php?user=${user.id}`;
  });
  const div2 = document.createElement("div");
  const h3Name = document.createElement("h3");
  const pId = document.createElement("p");
  const adminButton = document.createElement("button");
  const removeButton = document.createElement("button");
  const adminIcon = document.createElement("ion-icon");
  const removeIcon = document.createElement("ion-icon");

  adminButton.addEventListener("click", async () => {
    try {
      addAdmin(user.id);
      const adminsList = document.getElementById("admins-list");
      const usersList = document.getElementById("users-list");
      const noItemsFoundLi = document.querySelector(
        "#admins-list .no-users-found"
      );
      if (noItemsFoundLi) adminsList.innerHTML = "";
      adminsList.appendChild(renderAdmin(user));
      li.remove();
      renderNoUsersFound(usersList);
    } catch (error) {
      console.error("Error creating admin:", error);
    }
  });

  removeButton.addEventListener("click", async () => {
    try {
      removeUser(user.id);
      const usersList = document.getElementById("users-list");
      li.remove();
      renderNoUsersFound(usersList);
    } catch (error) {
      console.error("Error removing admin:", error);
    }
  });

  h3Name.textContent = `${user.first_name} ${user.last_name}`;
  pId.textContent = `#${user.id}`;
  adminIcon.name = "star-outline";
  removeIcon.name = "trash-outline";
  div1.appendChild(h3Name);
  div1.appendChild(pId);
  if (user.id != sessionId) {
    div2.appendChild(adminButton);
    div2.appendChild(removeButton);
  }
  adminButton.appendChild(adminIcon);
  adminButton.title = "Remover cargo";
  removeButton.appendChild(removeIcon);
  removeButton.title = "Apagar utilizador";
  li.appendChild(div1);
  li.appendChild(div2);

  return li;
};

const renderNoUsersFound = (element) => {
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
    li.className = "no-users-found";
    element.appendChild(li);
  }
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

const addAdmin = (id) => {
  fetch(`./../api/user/index.php?&admin=1`, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: id, admin: true }),
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

const removeAdmin = (id) => {
  fetch(`./../api/user/index.php?&admin=1`, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: id, admin: false }),
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

const removeUser = (id) => {
  fetch(`./../api/user/index.php?&id=${id}`, {
    method: "DELETE",
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

const getCategories = async () => {
  return fetch(`./../api/category/index.php`, {
    method: "GET",
  })
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

const removeCategory = (categoryId) => {
  fetch(`./../api/category/index.php?&id=${categoryId}`, {
    method: "DELETE",
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
