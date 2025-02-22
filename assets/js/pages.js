// Constants for API endpoints and messages
const API_ENDPOINTS = {
  LOAD_DATA: (loadType, aid) =>
    `api/v1/loadData.php?load=${loadType}&aid=${aid}`,
  DELETE: (id, type) => `api/v1/delete.php?id=${id}&t=${type}`,
  ACTIVATE: (id, type, action) =>
    `api/v1/activate.php?id=${id}&t=pages&action=${action}`,
};

const MESSAGES = {
  DELETE_SUCCESS: "Record deleted successfully!",
  BAN_SUCCESS: "Post deactivated successfully!",
  UNBAN_SUCCESS: "Post activated successfully!",
  ERROR: "An error occurred.",
};

// Utility function to format dates
const formatDate = (dateString) => {
  if (!dateString) return "-";

  const date = new Date(dateString);
  if (isNaN(date.getTime())) return "-";

  const options = { year: "numeric", month: "long", day: "numeric" };
  return date.toLocaleString(navigator.language, options);
};

// Fetch data from the API
const fetchData = async (loadType, aid) => {
  try {
    const response = await fetch(API_ENDPOINTS.LOAD_DATA(loadType, aid));
    if (!response.ok) throw new Error(`Error: ${response.status}`);

    const { status, data, message } = await response.json();
    if (status !== "success" || !Array.isArray(data)) {
      throw new Error(message || "Unexpected response format.");
    }

    return data;
  } catch (error) {
    console.error("Error fetching data:", error);
    throw error;
  }
};

// Render data into the table
const renderTable = (data, tableBody) => {
  if (data.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="9" class="text-center">No data available</td>
      </tr>
    `;
    return;
  }

  tableBody.innerHTML = data
    .map(
      (record) => `
      <tr>
        <td>${record.pageName}</td>
        <td>${record.pageDescription}</td>
        <td>${record.pageDetails}</td>
        <td>${record.pagePosition}</td>
        <td>${record.admin_id}</td>
        <td>${formatDate(record.created_at)}</td>
        <td>${formatDate(record.updated_at) || "-"}</td>
        <td>
          ${
            record.is_active
              ? "<span class='text-success font-weight-bold'>Yes</span>"
              : "<span class='text-danger font-weight-bold'>No</span>"
          }
        </td>
        <td>
          <a href="edit.php?edit=page&id=${
            record.id
          }" class="btn btn-primary edit-btn m-1">
            <i class='bi bi-pencil'></i>
          </a>
          <button class="btn btn-danger delete-btn m-1" data-id="${record.id}">
            <i class='bi bi-trash'></i>
          </button>
          ${
            record.is_active === 0
              ? `<button class="btn btn-success unban-btn m-1" data-id="${record.id}">
                  <i class='bi bi-check-circle'></i> Activate
                </button>`
              : `<button class="btn btn-warning ban-btn m-1" data-id="${record.id}">
                  <i class='bi bi-x-circle'></i> Deactivate
                </button>`
          }
        </td>
      </tr>
    `
    )
    .join("");
};

// Handle API actions (delete, ban, unban)
const handleApiAction = async (url, method, successMessage) => {
  try {
    const response = await fetch(url, { method });
    const result = await response.json();

    if (response.ok) {
      Swal.fire({
        title: "Success!",
        text: result.message || successMessage,
        icon: "success",
        confirmButtonText: "OK",
      });
      return true;
    } else {
      Swal.fire({
        title: "Error!",
        text: result.message || MESSAGES.ERROR,
        icon: "error",
        confirmButtonText: "OK",
      });
      return false;
    }
  } catch (error) {
    Swal.fire({
      title: "An error occurred",
      text: error.message,
      icon: "error",
      confirmButtonText: "OK",
    });
    return false;
  }
};

// Handle delete action
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
    const success = await handleApiAction(
      API_ENDPOINTS.DELETE(id, "pages"),
      "DELETE",
      MESSAGES.DELETE_SUCCESS
    );
    if (success) fetchAndDisplayData("pages", adminId);
  }
};

// Handle ban action
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
    const success = await handleApiAction(
      API_ENDPOINTS.ACTIVATE(id, "faq", "ban"),
      "POST",
      MESSAGES.BAN_SUCCESS
    );
    if (success) fetchAndDisplayData("pages", adminId);
  }
};

// Handle unban action
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
    const success = await handleApiAction(
      API_ENDPOINTS.ACTIVATE(id, "faq", "unban"),
      "POST",
      MESSAGES.UNBAN_SUCCESS
    );
    if (success) fetchAndDisplayData("pages", adminId);
  }
};

// Attach event listeners for action buttons using event delegation
const attachActionHandlers = () => {
  document.querySelector(".data_table").addEventListener("click", (event) => {
    const target = event.target.closest("button");
    if (!target) return;

    const id = target.dataset.id;
    if (target.classList.contains("delete-btn")) handleDelete(id);
    if (target.classList.contains("ban-btn")) handleBan(id);
    if (target.classList.contains("unban-btn")) handleUnban(id);
  });
};

// Fetch and display data
const fetchAndDisplayData = async (loadType, aid) => {
  const tableBody = document.querySelector(".data_table tbody");
  try {
    const data = await fetchData(loadType, aid);
    renderTable(data, tableBody);
    attachActionHandlers();
  } catch (error) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="9" class="text-center">Error loading data</td>
      </tr>
    `;
  }
};

// Fetch and display data on page load
fetchAndDisplayData("pages", adminId);
