document.addEventListener("DOMContentLoaded", function () {
  const notifyButton = document.getElementById("slack-notify-button");

  if (!notifyButton) return;

  notifyButton.addEventListener("click", function (event) {
    event.preventDefault();

    // Get data from the button's data attributes
    const pageName = notifyButton.getAttribute("data-page-name");
    const pageUrl = notifyButton.getAttribute("data-page-url");
    const csrfToken = document.querySelector('meta[name="token"]').content;

    notifyButton.disabled = true;

    fetch("/ajax/training-complete", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
      },
      body: JSON.stringify({
        page_name: pageName,
        page_url: pageUrl,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "ok") {
          alert("Chapter successfully completed!");
          notifyButton.innerHTML = "Completed"; // Optional: change button text
        } else {
          alert("Error sending notification.");
          notifyButton.disabled = false;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        notifyButton.disabled = false;
      });
  });
});
