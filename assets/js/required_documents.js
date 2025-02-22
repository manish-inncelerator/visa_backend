(function () {
  ("use strict");

  // Initialize the script when the DOM is fully loaded
  document.addEventListener("DOMContentLoaded", () => {
    attachFormSubmissionListener();
    attachEditFormSubmissionListener();
    fetchAndDisplayRequiredDocuments();
  });

  // Function to fetch and display required documents
  const fetchAndDisplayRequiredDocuments = async () => {
    try {
      const response = await fetch(
        `api/v1/loadData.php?load=required_documents&aid=${adminId}`
      );
      if (!response.ok) throw new Error(`Error: ${response.status}`);

      const { status, data, message } = await response.json();

      if (status !== "success" || !Array.isArray(data)) {
        throw new Error(message || "Unexpected response format.");
      }

      renderRequiredDocuments(data);
      attachActionHandlers();
    } catch (error) {
      console.error("Error fetching required documents:", error);
    }
  };

  // Function to render required documents into the table
  const renderRequiredDocuments = (data) => {
    const tableBody = document.querySelector(".visa_table tbody");

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
        <td>${record.required_document_name}</td>
        <td>${record.admin_id}</td>
        <td>${formatDate(record.created_on)}</td>
        <td>${formatDate(record.edited_on)}</td>
      <td>
  ${
    record.is_active
      ? "<span class='text-success font-weight-bold'>Yes</span>"
      : "<span class='text-danger font-weight-bold'>No</span>"
  }
</td>
        <td>
            <button class="btn btn-primary edit-btn m-1" data-id="${record.id}">
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
                </button>
            `
                : `
                <button class="btn btn-warning ban-btn m-1" data-id="${record.id}">
                    <i class='bi bi-x-circle'></i> Deactivate
                </button>
            `
            }
        </td>
    </tr>
  `
        )
        .join("");
    }
  };

  // Function to format date strings
  const formatDate = (localDateTime) => {
    if (!localDateTime || localDateTime === "0000-00-00 00:00:00") {
      return "-";
    }

    try {
      const [date] = localDateTime.split(" ");
      const [year, month, day] = date.split("-");
      const localDate = new Date(year, month - 1, day);
      const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
      const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        timeZone: timeZone,
      };
      return localDate.toLocaleString("en-US", options);
    } catch (error) {
      console.error("Error formatting date:", error);
      return "-";
    }
  };

  // Attach action handlers for Edit and Delete buttons
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

  const handleEdit = (id) => {
    $("#editModal").modal("show");
    fetchUpdatedData(id);
  };

  const fetchUpdatedData = (postId = null) => {
    $.ajax({
      url: "forms/edit/required_documents.php",
      method: "GET",
      data: { post_id: postId },
      success: function (response) {
        $("#modalContent").html(response);
        attachEditFormSubmissionListener();
      },
      error: function () {
        alert("An error occurred while fetching data.");
      },
    });
  };

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
          `api/v1/activate.php?id=${id}&t=required_documents&action=ban`,
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
          fetchAndDisplayRequiredDocuments();
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
          `api/v1/activate.php?id=${id}&t=required_documents&action=unban`,
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
          fetchAndDisplayRequiredDocuments();
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
        const response = await fetch(
          `api/v1/delete.php?id=${id}&t=required_documents`,
          {
            method: "DELETE",
          }
        );

        const result = await response.json();

        if (response.ok) {
          Swal.fire({
            title: "Deleted!",
            text: result.message,
            icon: "success",
            confirmButtonText: "OK",
          });
          fetchAndDisplayRequiredDocuments();
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

  const attachFormSubmissionListener = () => {
    const form = document.getElementById("requiredDocumentForm");

    if (form) {
      form.addEventListener("submit", async (event) => {
        event.preventDefault();

        if (!form.checkValidity()) {
          form.classList.add("was-validated");
          return;
        }

        const requiredDocument = document
          .getElementById("requiredDocumentName")
          ?.value.trim();
        const adminID = document
          .querySelector('input[name="adminID"]')
          ?.value.trim();

        if (!requiredDocument || !adminID) {
          Swal.fire({
            title: "Error!",
            text: "All fields are required.",
            icon: "error",
            confirmButtonText: "OK",
          });
          return;
        }

        try {
          const response = await fetch("api/v1/add_required_document.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ requiredDocument, adminID }),
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
              fetchAndDisplayRequiredDocuments();
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

  const attachEditFormSubmissionListener = () => {
    const editForm = document.getElementById("editRequiredDocumentForm");

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
          const response = await fetch("api/v1/edit_required_document.php", {
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
              fetchAndDisplayRequiredDocuments();
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
