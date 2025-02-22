document.addEventListener("DOMContentLoaded", function () {
  const countryForm = document.getElementById("countryForm");
  const requiredDocumentsSelect = document.getElementById("requiredDocuments");

  // Choices.js initialization
  let choicesInstance = null;
  if (requiredDocumentsSelect) {
    choicesInstance = new Choices(requiredDocumentsSelect, {
      removeItemButton: true,
      placeholder: true,
      placeholderValue: "Select required documents",
      searchEnabled: true,
      maxItemCount: -1,
      duplicateItemsAllowed: false,
    });
  }

  // Form submission handler
  countryForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    event.stopPropagation(); // Prevent default Bootstrap validation

    // Dynamically handle required attributes for pricing fields
    handlePricingFieldsRequired();

    // Validate the form
    let isValid = validateForm(countryForm);

    if (isValid) {
      const submitButton = countryForm.querySelector('button[type="submit"]');
      const originalButtonText = submitButton.innerHTML;

      submitButton.disabled = true;
      submitButton.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';

      try {
        const formData = buildFormJson(countryForm);
        const response = await submitFormData(formData);

        handleSuccessResponse(response, countryForm);
      } catch (error) {
        handleErrorResponse(error);
      } finally {
        resetSubmitButton(submitButton, originalButtonText);
      }
    } else {
      showAlert(
        "warning",
        "Validation Error!",
        "Please check all required fields."
      );
    }
  });

  // Function to handle required attributes for pricing fields
  function handlePricingFieldsRequired() {
    const serviceability = document.getElementById("serviceability").value;
    const isServiceabilityEasy = serviceability === "easy";

    const pricingFields = ["portifyFees", "VFSService", "embassyFee"];

    pricingFields.forEach((fieldName) => {
      const field = countryForm.querySelector(`[name="${fieldName}"]`);
      if (field) {
        if (isServiceabilityEasy) {
          field.setAttribute("required", true); // Add required attribute
        } else {
          field.removeAttribute("required"); // Remove required attribute
        }
      }
    });
  }

  // Form validation
  function validateForm(form) {
    let isValid = true;

    // Validate all fields
    Array.from(form.elements).forEach((element) => {
      if (element.name && element.type !== "file") {
        // Check if the field is required and empty
        if (element.required && !element.value.trim()) {
          isValid = false;
          element.classList.add("is-invalid");
        } else {
          element.classList.remove("is-invalid");
        }
      }
    });

    // Add Bootstrap's was-validated class to show validation styles
    if (!isValid) {
      form.classList.add("was-validated");
    }

    return isValid;
  }

  // Build form data as JSON
  function buildFormJson(form) {
    const formJson = {
      adminID: document.getElementById("adminID").value,
      requiredDocuments: [],
    };

    // Get required documents from Choices.js
    if (choicesInstance) {
      formJson.requiredDocuments = choicesInstance.getValue(true);
    }

    // Add other form fields
    Array.from(form.elements).forEach((element) => {
      if (element.name && element.type !== "file") {
        formJson[element.name] = element.value;
      }
    });

    // Skip pricing fields if serviceability is not "easy"
    const serviceability = document.getElementById("serviceability").value;
    if (serviceability !== "easy") {
      delete formJson.portifyFees;
      delete formJson.VFSService;
      delete formJson.embassyFee;
    }

    return formJson;
  }

  // Submit form data
  async function submitFormData(formData) {
    const response = await fetch("api/v1/add_country.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    });

    if (!response.ok) {
      const data = await response.json();
      throw new Error(data.error || `HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  // Handle successful response
  function handleSuccessResponse(data, form) {
    if (data.success) {
      showAlert(
        "success",
        "Success!",
        data.message || "Country details added successfully!"
      );
      form.reset();
      form.classList.remove("was-validated");

      // Reset Choices.js if used
      if (choicesInstance) {
        choicesInstance.destroy();
        choicesInstance = new Choices(requiredDocumentsSelect, {
          removeItemButton: true,
          placeholder: true,
          placeholderValue: "Select required documents",
          searchEnabled: true,
          maxItemCount: -1,
          duplicateItemsAllowed: false,
        });
      }

      // Move to img.php to upload the images
      window.location.href = `img.php?id=${data.countryId}&country=${data.countryName}&query=${data.countryName}`;
    } else {
      throw new Error(data.error || "Unknown error occurred");
    }
  }

  // Handle error response
  function handleErrorResponse(error) {
    console.error("Error:", error);
    showAlert(
      "error",
      "Error!",
      error.message || "There was an error submitting the form."
    );
  }

  // Reset submit button
  function resetSubmitButton(button, originalText) {
    button.disabled = false;
    button.innerHTML = originalText;
  }

  // Show alert using SweetAlert2
  function showAlert(type, title, message) {
    Swal.fire({
      icon: type,
      title: title,
      text: message,
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 5000,
      timerProgressBar: true,
    });
  }
});

// Function to toggle pricing card visibility
function togglePricingCard() {
  const serviceability = document.getElementById("serviceability");
  const pricingCard = document.getElementById("pricingSection");

  if (serviceability.value === "easy") {
    pricingCard.classList.remove("d-none");
    pricingCard.classList.add("d-block"); // Show the pricing card
  } else {
    pricingCard.classList.add("d-none");
    pricingCard.classList.remove("d-block"); // Hide the pricing card
  }
}

// Add event listener to the serviceability dropdown
document
  .getElementById("serviceability")
  .addEventListener("change", togglePricingCard);

// Initialize visibility on page load
document.addEventListener("DOMContentLoaded", function () {
  togglePricingCard();
});
