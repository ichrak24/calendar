import { startStimulusApp } from "@symfony/stimulus-bundle";
// Importer les variables
import { saveReservationUrl, csrfToken } from "./app";
const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

document.addEventListener("DOMContentLoaded", function () {
  let startDate = null;
  let endDate = null;

  document.querySelectorAll(".calendar-grid .day").forEach((day) => {
    day.addEventListener("click", function () {
      const date = this.dataset.date;
      if (!startDate) {
        startDate = date;
        this.classList.add("selected-start");
      } else if (!endDate) {
        endDate = date;
        this.classList.add("selected-end");
        // Display reservation details input
        document.getElementById("reservationDetails").style.display = "block";
      } else {
        document
          .querySelectorAll(".selected-start, .selected-end")
          .forEach((el) =>
            el.classList.remove("selected-start", "selected-end")
          );
        startDate = date;
        endDate = null;
        this.classList.add("selected-start");
        // Hide reservation details input
        document.getElementById("reservationDetails").style.display = "none";
      }

      document.getElementById("selected-dates").textContent = `${
        startDate || ""
      } - ${endDate || ""}`;
      document.getElementById("reservation-info").classList.remove("hidden");
    });
  });

  document
    .getElementById("save-reservation")
    .addEventListener("click", function () {
      const title = document.getElementById("reservationTitle").value;
      const startTime = document.getElementById("reservationStartTime").value;
      const endTime = document.getElementById("reservationEndTime").value;

      if (!startDate || !endDate || !title || !startTime || !endTime) {
        alert("Please fill all the fields.");
        return;
      }

      fetch(saveReservationUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({
          title: title,
          startDate: `${startDate}T${startTime}`,
          endDate: `${endDate}T${endTime}`,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert("Reservation saved successfully.");
            location.reload();
          } else {
            alert("Failed to save reservation.");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred. Please try again.");
        });
    });
});
