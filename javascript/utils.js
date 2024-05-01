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

const handlePriceInput = (element) => {
  element.addEventListener("input", (event) => {
    var inputValue = event.target.value;

    // Remove any non-digit or non-decimal characters except the first dot
    inputValue = inputValue.replace(/[^\d.]/g, "");

    // Ensure there's only one dot
    var dotIndex = inputValue.indexOf(".");
    if (dotIndex !== -1) {
      inputValue =
        inputValue.substr(0, dotIndex + 1) +
        inputValue.substr(dotIndex + 1).replace(/\./g, "");
    }

    // Remove leading zero if followed by another digit
    inputValue = inputValue.replace(/^0+(?=\d)/, "");

    // Ensure it's a positive number with up to two decimal places
    var decimalRegex = /^\d*\.?\d{0,2}$/;
    if (!decimalRegex.test(inputValue)) {
      // If not a valid number, set value to empty
      inputValue = "0";
    }

    // Update the input value
    element.value = inputValue;
  });
};
