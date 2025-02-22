document
  .getElementById("editpageForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Check form validity using Bootstrap validation
    const form = event.target;
    if (!form.checkValidity()) {
      form.classList.add("was-validated"); // Add Bootstrap validation styles
      return; // Stop further execution if the form is invalid
    }

    // Form elements
    const pageId = document.getElementById("pageId").value.trim(); // Hidden input for page ID
    const pageName = document.getElementById("pageName").value.trim();
    const pageSlug = document.getElementById("pageSlug").value.trim();
    const admin_id = document.getElementById("admin_id").value.trim();
    const pageDescription = document
      .getElementById("pageDescription")
      .value.trim();
    const pagePosition = document.getElementById("pagePosition").value;

    // Get TinyMCE content
    const pageDetails = tinymce.get("pageDetails").getContent(); // Use TinyMCE API to get content

    // Prepare data
    const formData = {
      pageId, // Include the page ID for editing
      pageName,
      pageSlug,
      admin_id,
      pageDescription,
      pageDetails, // Include TinyMCE content
      pagePosition,
    };

    try {
      // Send API request to update the page
      const response = await fetch("api/v1/edit_page.php", {
        method: "PUT", // Use PUT or PATCH for editing
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
            text: "Page updated successfully!",
            icon: "success",
            confirmButtonText: "OK",
          }).then(() => {
            // Optionally redirect or reload the page
            // alert("done");
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
          text: "Failed to update page. Please try again later.",
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
