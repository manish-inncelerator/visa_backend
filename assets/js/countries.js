// Constants for API endpoints and messages
const API_ENDPOINTS = {
  LOAD_DATA: (aid) => `api/v1/countries.php?aid=${aid}`,
  DELETE: (id, type) => `api/v1/delete.php?id=${id}&t=${type}`,
  ACTIVATE: (id, type, action) =>
    `api/v1/activate.php?id=${id}&t=countries&action=${action}`,
};

const MESSAGES = {
  DELETE_SUCCESS: "Record deleted successfully!",
  BAN_SUCCESS: "Country deactivated successfully!",
  UNBAN_SUCCESS: "Country activated successfully!",
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
const fetchData = async (aid) => {
  try {
    const response = await fetch(API_ENDPOINTS.LOAD_DATA(aid));
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
  <td>${record.country_name}</td>
  <td>${record.serviceability === "easy" ? "Easy" : "Not Easy"}</td>
  <td>${record.visa_type_name}</td>
  <td>${record.visa_kind_name}</td>
  <td>${record.visa_category_name}</td>
  <td>${record.stay_duration} days</td>
  <td>${record.visa_validity} ${record.visa_validity_unit}</td>
  <td>
    ${
      record.visa_entry.charAt(0).toUpperCase() +
      record.visa_entry.slice(1).toLowerCase()
    } Entry
  </td>
  <td>
    ${
      record.visa_department.toLowerCase() === "both"
        ? "Inbound & Outbound"
        : record.visa_department
            .split(" ") // Split the string into words
            .map((word, index) =>
              index === 0 ||
              word.toLowerCase() === "inbound" ||
              word.toLowerCase() === "outbound"
                ? word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
                : word.toLowerCase()
            )
            .join(" ") // Join the words back into a single string
    }
  </td>
  <td>${record.processing_time_value} ${record.processing_time_unit}</td>
  <td>${record.approval_rate}%</td>
  <td>SGD ${record.portify_fees}</td>
  <td>SGD ${record.embassy_fee}</td>
  <td>SGD ${record.vfs_service_fees}</td>
  <td>${record.admin_id}</td>
  <td>${formatDate(record.created_at)}</td>
  <td>${formatDate(record.edited_at)}</td>
  <td>
    ${
      record.is_active
        ? "<span class='text-success font-weight-bold'>Yes</span>"
        : "<span class='text-danger font-weight-bold'>No</span>"
    }
  </td>
  <td>
    <a href="edit.php?edit=country&id=${
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
      API_ENDPOINTS.DELETE(id, "countries"),
      "DELETE",
      MESSAGES.DELETE_SUCCESS
    );
    if (success) fetchAndDisplayData(adminId);
  }
};

// Handle ban action
const handleBan = async (id) => {
  const confirmDeletion = await Swal.fire({
    title: "Are you sure?",
    text: "This action will deactivate this country!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, deactivate it!",
    cancelButtonText: "Cancel",
  });

  if (confirmDeletion.isConfirmed) {
    const success = await handleApiAction(
      API_ENDPOINTS.ACTIVATE(id, "countries", "ban"),
      "POST",
      MESSAGES.BAN_SUCCESS
    );
    if (success) fetchAndDisplayData(adminId);
  }
};

// Handle unban action
const handleUnban = async (id) => {
  const confirmDeletion = await Swal.fire({
    title: "Are you sure?",
    text: "This action will activate this country!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, activate it!",
    cancelButtonText: "Cancel",
  });

  if (confirmDeletion.isConfirmed) {
    const success = await handleApiAction(
      API_ENDPOINTS.ACTIVATE(id, "countries", "unban"),
      "POST",
      MESSAGES.UNBAN_SUCCESS
    );
    if (success) fetchAndDisplayData(adminId);
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
const fetchAndDisplayData = async (aid) => {
  const tableBody = document.querySelector(".data_table tbody");
  try {
    const data = await fetchData(aid);
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
fetchAndDisplayData(adminId);
