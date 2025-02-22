(function () {
  "use strict";

  // Initialize the script when the DOM is fully loaded
  document.addEventListener("DOMContentLoaded", () => {
    attachFormSubmissionListener();
    attachEditFormSubmissionListener();
    fetchAndDisplayFaqData();
  });

  // Function to fetch and display FAQ data
  const fetchAndDisplayFaqData = async () => {
    try {
      // Make the API call and store the response
      const response = await fetch(
        `api/v1/loadData.php?load=faq&aid=${adminId}`
      );

      // Check if the response is OK (status code 200-299)
      if (!response.ok) throw new Error(`Error: ${response.status}`);

      // Parse the JSON response
      const { status, data, message } = await response.json();

      // Validate the response format
      if (status !== "success" || !Array.isArray(data)) {
        throw new Error(message || "Unexpected response format.");
      }

      // Render the FAQ data
      renderFaqData(data);

      // Attach action handlers (e.g., for edit/delete buttons)
      attachActionHandlers();
    } catch (error) {
      console.error("Error fetching FAQ data:", error);
    }
  };

  // Function to render FAQ data into the table
  const renderFaqData = (data) => {
    const tableBody = document.querySelector(".faq_table tbody");
    if (!tableBody) return;

    if (data.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center">No data available</td>
        </tr>
      `;
    } else {
      tableBody.innerHTML = data
        .map(
          (record) => `
            <tr>
                <td>${record.faqQuestion}</td>
                <td>${record.faqAnswer}</td>
                <td>${record.faqCountry}</td>
                <td>${record.admin_id}</td>
                <td>${formatDate(record.created_on)}</td>
                <td>${formatDate(record.edited_on)}</td>
                <td>${
                  record.is_active
                    ? "<span class='text-success font-weight-bold'>Yes</span>"
                    : "<span class='text-danger font-weight-bold'>No</span>"
                }</td>
                <td>
                    <button class="btn btn-primary edit-btn m-1" data-id="${
                      record.id
                    }">
                        <i class='bi bi-pencil'></i>
                    </button>
                    <button class="btn btn-danger delete-btn m-1" data-id="${
                      record.id
                    }">
                        <i class='bi bi-trash'></i>
                    </button>
                    ${
                      record.is_active === 0
                        ? `
                        <button class="btn btn-success unban-btn m-1" data-id="${record.id}">
                            <i class='bi bi-check-circle'></i> Activate
                        </button>`
                        : `
                        <button class="btn btn-warning ban-btn m-1" data-id="${record.id}">
                            <i class='bi bi-x-circle'></i> Deactivate
                        </button>`
                    }
                </td>
            </tr>
          `
        )
        .join("");
    }
  };

  // Date format
  const formatDate = (localDateTime) => {
    // Handle invalid or empty dates
    if (!localDateTime || localDateTime === "0000-00-00 00:00:00") {
      return "-";
    }

    try {
      // Convert the local date-time string to a Date object
      // Assuming the format is "YYYY-MM-DD HH:mm:ss"
      const [date] = localDateTime.split(" ");
      const [year, month, day] = date.split("-");

      // Create a Date object in local time
      const localDate = new Date(year, month - 1, day);

      // Get the local timezone (e.g., Asia/Kolkata)
      const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

      // Format options for the date, excluding time
      const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        timeZone: timeZone, // Set the time zone
      };

      // Get formatted date in local timezone
      const formattedDate = localDate.toLocaleString("en-US", options);

      return formattedDate;
    } catch (error) {
      console.error("Error formatting date:", error);
      return "-";
    }
  };

  // Attach action handlers for Edit, Delete, Ban, and Unban buttons
  const attachActionHandlers = () => {
    document
      .querySelectorAll(".edit-btn")
      .forEach((btn) =>
        btn.addEventListener("click", () => handleEdit(btn.dataset.id))
      );
    document
      .querySelectorAll(".delete-btn")
      .forEach((btn) =>
        btn.addEventListener("click", () => handleDelete(btn.dataset.id))
      );
    document
      .querySelectorAll(".ban-btn")
      .forEach((btn) =>
        btn.addEventListener("click", () => handleBan(btn.dataset.id))
      );
    document
      .querySelectorAll(".unban-btn")
      .forEach((btn) =>
        btn.addEventListener("click", () => handleUnban(btn.dataset.id))
      );
  };

  // Handle Edit button click
  const handleEdit = (id) => {
    $("#editModal").modal("show");
    fetchUpdatedData(id);
  };

  // Fetch updated data for editing
  const fetchUpdatedData = (postId = null) => {
    $.ajax({
      url: "forms/edit/faq.php",
      method: "GET",
      data: { post_id: postId },
      success: (response) => {
        $("#modalContent").html(response);
        attachEditFormSubmissionListener();
      },
      error: () => {
        alert("An error occurred while fetching data.");
      },
    });
  };

  // Handle Ban button click
  const handleBan = async (id) => {
    const confirmDeletion = await Swal.fire({
      title: "Are you sure?",
      text: "This action will deactivate this post!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, deactivate it!",
      cancelButtonText: "Cancel",
    });

    if (confirmDeletion.isConfirmed) {
      try {
        const response = await fetch(
          `api/v1/activate.php?id=${id}&t=faq&action=ban`,
          {
            method: "POST",
          }
        );

        const result = await response.json();

        if (response.ok) {
          Swal.fire({
            title: "Deactivated!",
            text: result.message,
            icon: "success",
            confirmButtonText: "OK",
          });
          fetchAndDisplayFaqData();
        } else {
          Swal.fire({
            title: "Error!",
            text: result.message || "An error occurred.",
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      } catch (error) {
        Swal.fire({
          title: "An error occurred",
          text: error.message,
          icon: "error",
          confirmButtonText: "OK",
        });
      }
    }
  };

  // Handle Unban button click
  const handleUnban = async (id) => {
    const confirmDeletion = await Swal.fire({
      title: "Are you sure?",
      text: "This action will activate this post!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, activate it!",
      cancelButtonText: "Cancel",
    });

    if (confirmDeletion.isConfirmed) {
      try {
        const response = await fetch(
          `api/v1/activate.php?id=${id}&t=faq&action=unban`,
          {
            method: "POST",
          }
        );

        const result = await response.json();

        if (response.ok) {
          Swal.fire({
            title: "Activated!",
            text: result.message,
            icon: "success",
            confirmButtonText: "OK",
          });
          fetchAndDisplayFaqData();
        } else {
          Swal.fire({
            title: "Error!",
            text: result.message || "An error occurred.",
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      } catch (error) {
        Swal.fire({
          title: "An error occurred",
          text: error.message,
          icon: "error",
          confirmButtonText: "OK",
        });
      }
    }
  };

  // Handle Delete button click
  const handleDelete = async (id) => {
    const confirmDeletion = await Swal.fire({
      title: "Are you sure?",
      text: "This action cannot be undone!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel",
    });

    if (confirmDeletion.isConfirmed) {
      try {
        const response = await fetch(`api/v1/delete.php?id=${id}&t=faq`, {
          method: "DELETE",
        });

        const result = await response.json();

        if (response.ok) {
          Swal.fire({
            title: "Deleted!",
            text: result.message,
            icon: "success",
            confirmButtonText: "OK",
          });
          fetchAndDisplayFaqData();
        } else {
          Swal.fire({
            title: "Error!",
            text: result.message || "An error occurred.",
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      } catch (error) {
        Swal.fire({
          title: "An error occurred",
          text: error.message,
          icon: "error",
          confirmButtonText: "OK",
        });
      }
    }
  };

  // Attach form submission listener for Add form
  const attachFormSubmissionListener = () => {
    const form = document.getElementById("faqForm");

    if (form) {
      form.addEventListener("submit", async (event) => {
        event.preventDefault();

        if (!form.checkValidity()) {
          form.classList.add("was-validated");
          return;
        }

        const faqQuestion = document
          .getElementById("faqQuestion")
          ?.value.trim();
        const faqAnswer = document.getElementById("faqAnswer")?.value.trim();
        const faqCountry = document.getElementById("faqCountry")?.value.trim();
        const adminID = document
          .querySelector('input[name="adminID"]')
          ?.value.trim();

        if (!faqQuestion || !faqAnswer || !faqCountry) {
          Swal.fire({
            title: "Error!",
            text: "All fields are required.",
            icon: "error",
            confirmButtonText: "OK",
          });
          return;
        }

        try {
          const response = await fetch("api/v1/add_faq.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              faqQuestion,
              faqAnswer,
              faqCountry,
              adminID,
            }),
          });

          const result = await response.json();

          if (response.ok) {
            Swal.fire({
              title: "Success!",
              text: result.message,
              icon: "success",
              confirmButtonText: "OK",
            }).then(() => {
              $("#addModal").modal("hide");
              form.reset();
              fetchAndDisplayFaqData();
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: result.message || "An unexpected error occurred.",
              icon: "error",
              confirmButtonText: "OK",
            });
          }
        } catch (error) {
          Swal.fire({
            title: "An error occurred",
            text: error.message,
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      });
    }
  };

  // Attach form submission listener for Edit form
  const attachEditFormSubmissionListener = () => {
    const editForm = document.getElementById("editfaqForm");

    if (editForm) {
      editForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        if (!editForm.checkValidity()) {
          editForm.classList.add("was-validated");
          return;
        }

        const formData = new FormData(editForm);
        const formDataObj = Object.fromEntries(formData.entries());

        try {
          const response = await fetch("api/v1/edit_faq.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(formDataObj),
          });

          const result = await response.json();

          if (response.ok) {
            Swal.fire({
              title: "Success!",
              text: result.message,
              icon: "success",
              confirmButtonText: "OK",
            }).then(() => {
              $("#editModal").modal("hide");
              fetchAndDisplayFaqData();
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: result.message || "An unexpected error occurred.",
              icon: "error",
              confirmButtonText: "OK",
            });
          }
        } catch (error) {
          Swal.fire({
            title: "An error occurred",
            text: error.message,
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      });
    }
  };
})();
