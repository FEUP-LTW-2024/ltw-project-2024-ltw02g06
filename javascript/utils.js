const getTimeAgo = (timestamp) => {
  const currentTime = new Date();
  const timestampTime = new Date(timestamp.date).toLocaleString("en-US", {
    timeZone: "Europe/Paris",
  });
  const adjustedTimestamp = new Date(timestampTime);

  let timeDifference = currentTime - adjustedTimestamp;
  let seconds = Math.floor(timeDifference / 1000);
  let minutes = Math.floor(seconds / 60);
  let hours = Math.floor(minutes / 60);
  let days = Math.floor(hours / 24);
  let months = Math.floor(days / 30.44);
  let years = Math.floor(months / 12);

  let timeAgo = "há ";
  if (years > 0) {
    timeAgo += years + (years === 1 ? " ano" : " anos");
  } else if (months > 0) {
    timeAgo += months + "m";
  } else if (days > 0) {
    timeAgo += days + "d";
  } else if (hours > 0) {
    timeAgo += hours + "h";
  } else if (minutes > 0) {
    timeAgo += minutes + "min";
  } else if (seconds > 0) {
    timeAgo += seconds + "s";
  } else {
    timeAgo = "agora";
  }

  return timeAgo;
};

const validatePriceInput = (inputElement) => {
  inputElement.addEventListener("input", () => {
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
  });
};

const validateFloatInput = (inputElement) => {
  inputElement.addEventListener("input", () => {
    var inputValue = inputElement.value;
    inputValue = inputValue.replace(/[^\d.-]/g, "");

    var dotIndex = inputValue.indexOf(".");
    if (dotIndex !== -1)
      inputValue =
        inputValue.substr(0, dotIndex + 1) +
        inputValue.substr(dotIndex + 1).replace(/\./g, "");

    inputValue = inputValue.replace(/^(-)?0+(?=\d)/, "$1");

    var decimalRegex = /^-?\d*\.?\d*$/;
    if (!decimalRegex.test(inputValue)) inputValue = "0";

    inputElement.value = inputValue;
  });
};

const validateIntInput = (inputElement) => {
  inputElement.addEventListener("input", () => {
    var inputValue = inputElement.value;
    inputValue = inputValue.replace(/[^\d-]/g, "");
    inputValue = inputValue.replace(/^(-)?0+(?=\d)/, "$1");

    var integerRegex = /^-?\d*$/;
    if (!integerRegex.test(inputValue)) inputValue = "0";

    inputElement.value = inputValue;
  });
};

const validateEmailInput = (inputElement) => {
  inputElement.addEventListener("blur", () => {
    var emailValue = inputElement.value.trim();

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailValue)) {
      inputElement.value = "";
    }
  });
};

const showModal = (message) => {
  const modal = document.getElementById("modal");
  const modalMessage = document.getElementById("modal-message");
  modalMessage.textContent = message;
  modal.style.display = "block";

  // Close the modal when anywhere outside of the modal is clicked
  window.onclick = function (event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };

  // Close the modal when the close button is clicked
  const closeBtn = document.querySelector(".close");
  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });
};

const isEmailAlreadyRegistered = async (email) => {
  return fetch(`./../api/user/email.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      email: email,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        return false;
      }
      return true;
    })
    .catch((error) => {
      console.error("There was an unexpected error:", error);
    });
};
