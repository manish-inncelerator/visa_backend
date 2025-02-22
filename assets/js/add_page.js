document
  .getElementById("pageForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Check form validity using Bootstrap validation classes
    const form = event.target;
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    // Form elements
    const pageName = document.getElementById("pageName").value.trim();
    const admin_id = document.getElementById("admin_id").value.trim();
    const pageDescription = document
      .getElementById("pageDescription")
      .value.trim();
    const pageDetails = tinymce.get("pageDetails").getContent();
    const pagePosition = document.getElementById("pagePosition").value;
    const pageSlug = document.getElementById("pageSlug").value;

    // Prepare data
    const formData = {
      pageName,
      admin_id,
      pageDescription,
      pageSlug,
      pageDetails,
      pagePosition,
    };

    try {
      // Send API request
      const response = await fetch("api/v1/add_page.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });

      // Handle response
      if (response.ok) {
        const result = await response.json();
        if (result.success) {
          Swal.fire({
            title: "Success!",
            text: "Page created successfully!",
            icon: "success",
            confirmButtonText: "OK",
          }).then(() => {
            // Reset the form after user closes the alert
            form.reset();
            form.classList.remove("was-validated");
            window.location.href = "view.php?view=pages"; // Redirect to the pages list
          });
        } else {
          Swal.fire({
            title: "Error!",
            text: result.message,
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      } else {
        Swal.fire({
          title: "Error!",
          text: "Failed to create page. Please try again later.",
          icon: "error",
          confirmButtonText: "OK",
        });
      }
    } catch (error) {
      console.error("Error:", error);
      Swal.fire({
        title: "Error!",
        text: "An unexpected error occurred. Please try again.",
        icon: "error",
        confirmButtonText: "OK",
      });
    }
  });
