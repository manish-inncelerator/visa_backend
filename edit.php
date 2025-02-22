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
$edit = isset($_GET['edit']) ? filter_var($_GET['edit'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
// Ensure the 'add' parameter contains only safe characters (alphanumeric, underscores, hyphens)
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $edit)) {
    $edit = ''; // Reset to empty if invalid characters are found
}

// Safely generate the text for the page title
$editTextify = $edit ? ucwords(str_replace(['_', '-'], ' ', $edit)) : '';
?>
<?php
echo html_head(
    "Edit " . htmlspecialchars($editTextify, ENT_QUOTES, 'UTF-8'), // Ensure safe output
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
                    <?php if ($edit === 'countries' || $edit === 'page'): ?>

                        <span>Edit <?= ucwords($editTextify); ?></span>

                    <?php else: ?>
                        <span><?= ucwords($editTextify); ?></span>

                    <?php endif; ?>
                    <div>
                        <?php if ($edit === 'page') {
                            $enhanchedAdd = 'view.php?view=pages';
                            $backButtonText = 'Back';
                        } else if ($edit === 'countries') {
                            $enhanchedAdd = 'view.php?view=countries';
                            $backButtonText = 'Back';
                        } else {
                            $enhanchedAdd = 'dashboard.php';
                            $backButtonText = 'Dashboard';
                        } ?>
                        <a href="<?= $enhanchedAdd; ?>" class="btn btn-dark"><i class="bi bi-chevron-left"></i> <?= $backButtonText; ?></a>
                        <?php if ($edit === 'countries' || $edit === 'page'): ?>
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
                        <?php if ($edit === 'countries'): ?>
                            <?php include "forms/edit/country.php"; ?>
                        <?php elseif ($edit === 'page'): ?>
                            <?php include "forms/edit/page.php"; ?>
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
    'countries' => 'assets/js/edit_country.js',
    'page' => 'assets/js/edit_page.js',

];

if (isset($scripts[$edit])) {
    $script = $scripts[$edit];
    $token = ($edit !== 'countries') ? '?token=' . bin2hex(random_bytes(8)) : '';
    echo "<script src=\"{$script}{$token}\"></script>";
}

// Include DataTable script everywhere except for the 'country' page

if ($edit !== 'countries' && $edit !== 'page') {
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

<?php if ($edit === 'page') { ?>
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