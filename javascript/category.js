document.addEventListener("DOMContentLoaded", () => {
  handleAddValueButtons();
  handleAddAttributeButton();
  handleCancelButton();
});

document
  .getElementById("edit-category-section")
  .addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
    }
  });

const handleCancelButton = () => {
  const cancelButton = document.getElementById("edit-category-cancel-btn");

  cancelButton.addEventListener("click", () => {
    window.location.href = "/pages/admin.php";
  });
};

const handleAddValueButtons = () => {
  const addValueButtons = document.querySelectorAll(".add-value-button");

  addValueButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const newValueInput = button.parentNode.querySelector(".new-value-input");
      const newValue = newValueInput.value.trim();
      if (newValue !== "") {
        const ul = button.parentNode.parentNode.querySelector("ul");
        const id =
          button.parentNode.parentNode.querySelector(".attribute-id").value;
        const li = document.createElement("li");
        const hiddenInputId = document.createElement("input");
        const valueId = uuidv4();
        hiddenInputId.type = "hidden";
        hiddenInputId.name = `attribute[${id}][values][${valueId}][id]`;
        hiddenInputId.value = "-1";
        const hiddenInputValue = document.createElement("input");
        hiddenInputValue.type = "hidden";
        hiddenInputValue.name = `attribute[${id}][values][${valueId}][value]`;
        hiddenInputValue.value = newValue;
        const p = document.createElement("p");
        p.textContent = newValue;
        const removeButton = document.createElement("button");
        removeButton.innerHTML = '<ion-icon name="close"></ion-icon>';
        removeButton.type = "button";
        removeButton.onclick = () => {
          removeLi(removeButton);
        };

        li.appendChild(hiddenInputId);
        li.appendChild(hiddenInputValue);
        li.appendChild(p);
        li.appendChild(removeButton);
        ul.appendChild(li);

        newValueInput.value = "";
      }
    });
  });
};

const removeLi = (button) => {
  const li = button.closest("li");
  const ul = li.parentNode;
  ul.removeChild(li);
};

const handleAddAttributeButton = () => {
  const addAttributeButton = document.getElementById("add-attribute-button");
  addAttributeButton.addEventListener("click", () => {
    const newAttributeNameInput = document.getElementById(
      "new-attribute-input"
    );
    const newAttributeTypeSelect =
      document.getElementById("new-attribute-type");
    const attributeName = newAttributeNameInput.value.trim();
    const attributeType = newAttributeTypeSelect.value;

    if (attributeName !== "") {
      const ul = document.querySelector("#edit-category-section ul");
      const li = document.createElement("li");
      const attributeId = uuidv4();
      const inputId = document.createElement("input");
      inputId.type = "hidden";
      inputId.name = `attribute[${attributeId}][id]`;
      inputId.value = "-1";
      const inputType = document.createElement("input");
      inputType.type = "hidden";
      inputType.name = `attribute[${attributeId}][type]`;
      inputType.value = attributeType;
      const inputName = document.createElement("input");
      inputName.type = "hidden";
      inputName.name = `attribute[${attributeId}][name]`;
      inputName.value = attributeName;
      const div = document.createElement("div");
      const h3 = document.createElement("h3");
      h3.textContent = attributeName;
      const p = document.createElement("p");
      p.textContent =
        attributeType === "enum"
          ? "Enumeração"
          : attributeType === "real"
          ? "Número Real"
          : attributeType === "int"
          ? "Número Inteiro"
          : "Texto";
      const button = document.createElement("button");
      button.type = "button";
      button.innerHTML = '<ion-icon name="trash-outline"></ion-icon>';
      button.onclick = () => {
        removeLi(button);
      };

      div.appendChild(h3);
      div.appendChild(p);
      div.appendChild(button);
      li.appendChild(inputId);
      li.appendChild(inputType);
      li.appendChild(inputName);
      li.appendChild(div);
      ul.appendChild(li);

      newAttributeNameInput.value = "";
      newAttributeTypeSelect.value = "default";

      if (attributeType === "enum") {
        const divEnum = document.createElement("div");
        const newValueInput = document.createElement("input");
        newValueInput.placeholder = "Valor";
        newValueInput.className = "new-value-input";
        const addValueButton = document.createElement("button");
        addValueButton.type = "button";
        addValueButton.className = "add-value-button";
        addValueButton.innerHTML = '<ion-icon name="add"></ion-icon>';
        addValueButton.addEventListener("click", () => {
          const newValue = newValueInput.value.trim();
          if (newValue !== "") {
            const ulEnum = li.querySelector("ul");
            const liEnum = document.createElement("li");
            const valueId = uuidv4();
            const hiddenInputId = document.createElement("input");
            hiddenInputId.type = "hidden";
            hiddenInputId.name = `attribute[${attributeId}][values][${valueId}][id]`;
            hiddenInputId.value = "-1";
            const hiddenInputValue = document.createElement("input");
            hiddenInputValue.type = "hidden";
            hiddenInputValue.name = `attribute[${attributeId}][values][${valueId}][value]`;
            hiddenInputValue.value = newValue;
            const pEnum = document.createElement("p");
            pEnum.textContent = newValue;
            const removeButton = document.createElement("button");
            removeButton.type = "button";
            removeButton.innerHTML = '<ion-icon name="close"></ion-icon>';
            removeButton.onclick = () => {
              removeLi(removeButton);
            };

            liEnum.appendChild(hiddenInputId);
            liEnum.appendChild(hiddenInputValue);
            liEnum.appendChild(pEnum);
            liEnum.appendChild(removeButton);
            ulEnum.appendChild(liEnum);

            newValueInput.value = "";
          }
        });

        const ulEnum = document.createElement("ul");
        li.appendChild(ulEnum);
        divEnum.appendChild(newValueInput);
        divEnum.appendChild(addValueButton);
        li.appendChild(divEnum);
      }
    }
  });
};
