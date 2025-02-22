<?php
// Start the session
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to the login page if the user is not logged in
    header('Location: login.html');
    exit;
}
?>

<!-- Include HTML -->
<?php include 'inc/html_head.php'; ?>
<?php include 'inc/html_foot.php'; ?>

<?php
// Sanitize and validate the 'add' parameter
$add = isset($_GET['add']) ? filter_var($_GET['add'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
// Ensure the 'add' parameter contains only safe characters (alphanumeric, underscores, hyphens)
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $add)) {
    $add = ''; // Reset to empty if invalid characters are found
}

// Safely generate the text for the page title
$addTextify = $add ? ucwords(str_replace(['_', '-'], ' ', $add)) : '';
?>
<?php
echo html_head(
    "Add " . htmlspecialchars($addTextify, ENT_QUOTES, 'UTF-8'), // Ensure safe output
    null,
    false, // Include Chart.js
    true // Include FontAwesome
);
?>


<div class="dash">
    <div class="dash-nav dash-nav-dark">
        <!-- Sidebar -->
        <?php include 'components/Sidebar.php'; ?>
        <!-- ./Sidebar -->
    </div>
    <div class="dash-app">
        <header class="dash-toolbar">
            <!-- Menubar -->
            <?php include 'components/Menubar.php'; ?>
            <!-- ./Menubar -->
        </header>
        <main class="dash-content">
            <div class="container-fluid">
                <h1 class="d-flex justify-content-between align-items-center mb-3 fw-bold h4">
                    <?php if ($add === 'countries' || $add === 'page'): ?>

                        <span>Add <?= ucwords($addTextify); ?></span>

                    <?php else: ?>
                        <span><?= ucwords($addTextify); ?></span>

                    <?php endif; ?>
                    <div>
                        <?php if ($add === 'pages') {
                            $enhanchedAdd = 'view.php?view=pages';
                            $backButtonText = 'Back';
                        } else if ($add === 'countries') {
                            $enhanchedAdd = 'view.php?view=countries';
                            $backButtonText = 'Back';
                        } else {
                            $enhanchedAdd = 'dashboard.php';
                            $backButtonText = 'Dashboard';
                        } ?>
                        <a href="<?= $enhanchedAdd; ?>" class="btn btn-dark"><i class="bi bi-chevron-left"></i> <?= $backButtonText; ?></a>
                        <?php if ($add === 'countries' || $add === 'pages'): ?>
                            <a href="view.php?view=<?= $add; ?>" class="btn btn-info ml-1"><i class="bi bi-eye"></i> View All</a>
                        <?php else: ?>
                            <a href="#"
                                class="btn btn-info ml-1"
                                data-toggle="modal"
                                data-target="#addModal">
                                <i class="bi bi-plus-circle"></i> Add New
                            </a> <?php endif; ?>
                    </div>
                </h1>

                <div class="row">
                    <div class="col-12">
                        <?php if ($add === 'countries'): ?>
                            <?php include "forms/add/country.php"; ?>
                        <?php elseif ($add === 'pages'): ?>
                            <?php include "forms/add/page.php"; ?>
                        <?php elseif ($add === 'visa_type' || $add === 'visa_kind' || $add === 'required_documents' || $add === 'faq' || $add === 'visa_category'): ?>
                            <?php
                            $_GET['show'] = $add;  // Set the parameter
                            include "forms/view/show.php";  // Include the file
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<!-- Close HTML -->

<script>
    const adminId = "<?php echo $_SESSION['admin_id']; ?>";
</script>
<?php
echo html_scripts(true, true, true, "assets/js/spur.js", true);
?>


<?php
$scripts = [
    'countries' => 'assets/js/add_country.js',
    'pages' => 'assets/js/add_page.js',
    'visa_type' => 'assets/js/visa_type.js',
    'visa_kind' => 'assets/js/visa_kind.js',
    'visa_category' => 'assets/js/visa_category.js',
    'faq' => 'assets/js/faq.js',
    'required_documents' => 'assets/js/required_documents.js',
];


if (isset($scripts[$add])) {
    $script = $scripts[$add];
    $token = ($add !== 'countries') ? '?token=' . bin2hex(random_bytes(8)) : '';
    echo "<script src=\"{$script}{$token}\"></script>";
}

// Include DataTable script everywhere except for the 'country' page


if ($add !== 'countries' && $add !== 'pages') {
    echo '<script>
        $(document).ready(function() {
            // Use a timeout to delay DataTable initialization
            setTimeout(function() {
                // Initialize DataTable only if there are rows
                if ($("#recordTable tbody tr").length > 0) {
                    var table = $("#recordTable").DataTable({
                        responsive: true,
                        autoWidth: false,
                        paging: true,
                        searching: true,
                        ordering: false,
                        info: true,
                        lengthChange: true,
                        pageLength: 10
                    });

                    // Redraw the table to update row count after dynamic data is added
                    table.draw();
                }
            }, 1000); // Adjust timeout delay as needed (1 second in this case)
        });
    </script>';
}
?>

<?php if ($add === 'countries') { ?>
    <script>
        // Get references to the input fields
        const portifyFees = document.getElementById('portifyFees');
        const VFSService = document.getElementById('VFSService');
        const onlyEmbassyFee = document.getElementById('onlyEmbassyFee');
        const totalPricing = document.getElementById('totalPricing');

        // Function to calculate the total
        function calculateTotal() {
            // Get the values from the input fields and convert them to numbers
            const portify = parseFloat(portifyFees.value) || 0;
            const vfs = parseFloat(VFSService.value) || 0;
            const embassy = parseFloat(onlyEmbassyFee.value) || 0;

            // Calculate the total sum
            const total = portify + vfs + embassy;

            // Update the totalPricing field
            totalPricing.innerHTML = total;
        }

        // Add event listeners to the input fields
        portifyFees.addEventListener('input', calculateTotal);
        VFSService.addEventListener('input', calculateTotal);
        onlyEmbassyFee.addEventListener('input', calculateTotal);
    </script>
<?php } ?>

<?php if ($add === 'pages') { ?>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@5.1.0/tinymce.min.js" defer></script>
    <script>
        // convert pageName to slug
        document.getElementById("pageName").addEventListener("input", function() {
            let pageName = this.value.trim(); // Trim spaces from start & end

            let slug = pageName
                .toLowerCase()
                .replace(/[^a-z0-9\s-_]/g, "") // Allow alphanumeric, spaces, hyphens, and underscores
                .replace(/\s+/g, "-") // Convert spaces to hyphens
                .replace(/-+/g, "-") // Remove multiple consecutive hyphens
                .replace(/^[-_]+|[-_]+$/g, ""); // Trim leading/trailing hyphens & underscores

            document.getElementById("pageSlug").value = slug;
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#pageDetails',
                plugins: 'lists link image table code media', // Added 'image' plugin
                toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | media', // Added 'image' to toolbar
                height: 450,
                menubar: false, // Disables the menubar
                branding: false, // Removes the "Powered by Tiny" branding
                images_upload_url: 'api/v1/upload_image.php', // URL to your server-side image upload handler
                images_upload_base_path: '', // Optional: Base path for the uploaded images
                automatic_uploads: true, // Automatically uploads images when inserted
                file_picker_types: 'image', // Specifies that only images can be picked
                file_picker_callback: function(callback, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.onchange = function() {
                        var file = input.files[0];
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            callback(e.target.result, {
                                alt: file.name
                            });
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                }
            });
        });
    </script>
<?php } ?>



</body>

</html>