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
$view = isset($_GET['view']) ? filter_var($_GET['view'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
// Ensure the 'add' parameter contains only safe characters (alphanumeric, underscores, hyphens)
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $view)) {
    $view = ''; // Reset to empty if invalid characters are found
}

// Safely generate the text for the page title
$viewTextify = $view ? ucwords(str_replace(['_', '-'], ' ', $view)) : '';
?>
<?php
echo html_head(
    " " . htmlspecialchars($viewTextify, ENT_QUOTES, 'UTF-8'), // Ensure safe output
    null,
    false, // Include Chart.js
    true // Include FontAwesome
);
?>

<?php
if ($view === 'admins') {
    //require database  connection
    require 'Database.php';
    // fetch admin record
    $admin_id = $_SESSION['admin_id'];
    $admin = $database->get("admins", "*", ["admin_id" => $admin_id]);
}
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
                    <?php if ($view === 'countries' || $view === 'pages' || $view === 'admins'): ?>

                        <span> <?= ucwords($viewTextify); ?></span>

                    <?php endif; ?>
                    <div>
                        <?php if ($view === 'countries' || $view === 'pages'): ?>
                            <a href="add.php?add=<?= $view; ?>" class="btn btn-info ml-1">
                                <i class="bi bi-plus-circle"></i> Add New
                            </a>
                        <?php endif; ?>
                        <?php if ($view === 'admins'): ?>
                            <?php
                            // Check if admin_role is 'master' (single or comma-separated)
                            $isMaster = false;
                            if (!empty($admin['admin_role'])) {
                                // Convert to array if it's a string
                                $roles = is_array($admin['admin_role']) ? $admin['admin_role'] : explode(',', $admin['admin_role']);
                                // Trim whitespace and check if 'master' exists
                                $roles = array_map('trim', $roles);
                                $isMaster = in_array('master', $roles);
                            }
                            ?>
                            <?php if ($isMaster): ?>
                                <a href="signup.html?mid=<?= $_SESSION['admin_id']; ?>" class="btn btn-info ml-1">
                                    <i class="bi bi-plus-circle"></i> Add New
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </h1>
                <div class="row">
                    <div class="col">
                        <?php if (isset($_GET['view']) && $_GET['view'] === 'pages') { ?>
                            <table id="data_table" class="table table-bordered bg-white data_table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Page Name</th>
                                        <th>Page Description</th>
                                        <th>Page Content</th>
                                        <th>page Position</th>
                                        <th>Added by</th>
                                        <th>Added On</th>
                                        <th>Edited On</th>
                                        <th>Is Active?</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th>Page Name</th>
                                        <th>Page Description</th>
                                        <th>Page Content</th>
                                        <th>page Position</th>
                                        <th>Added by</th>
                                        <th>Added On</th>
                                        <th>Edited On</th>
                                        <th>Is Active?</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php } elseif (isset($_GET['view']) && $_GET['view'] === 'countries') { ?>

                            <table id="data_table" class="table table-bordered bg-white data_table">
                                <thead class="table-dark">
                                    <tr>

                                        <th>Country Name</th>
                                        <th>Serviceability</th>
                                        <th>Visa Type</th>
                                        <th>Visa Kind</th>
                                        <th>Visa Category</th>
                                        <th>Stay Duration</th>
                                        <th>Visa Validity</th>
                                        <th>Visa Entry</th>
                                        <th>Visa Department</th>
                                        <th>Processing Time </th>
                                        <th>Approval Rate</th>
                                        <th>Our Fee</th>
                                        <th>Embassy Fee</th>
                                        <th>VFS Service Fee</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Edited At</th>
                                        <th>Active?</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Table rows will go here -->
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th>Country Name</th>
                                        <th>Serviceability</th>
                                        <th>Visa Type</th>
                                        <th>Visa Kind</th>
                                        <th>Visa Category</th>
                                        <th>Stay Duration</th>
                                        <th>Visa Validity</th>
                                        <th>Visa Entry</th>
                                        <th>Visa Department</th>
                                        <th>Processing Time </th>
                                        <th>Approval Rate</th>
                                        <th>Our Fee</th>
                                        <th>Embassy Fee</th>
                                        <th>VFS Service Fee</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Edited At</th>
                                        <th>Active?</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php } else { ?>

                            <table id="data_table" class="table table-bordered bg-white data_table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Admin Name</th>
                                        <th>Admin Email</th>
                                        <th>Admin Role</th>
                                        <th>Admin Deptt.</th>
                                        <th>Registered On</th>
                                        <th>Last Logged in</th>
                                        <th>Is Active?</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th>Admin Name</th>
                                        <th>Admin Email</th>
                                        <th>Admin Role</th>
                                        <th>Admin Deptt.</th>
                                        <th>Registered On</th>
                                        <th>Last Logged in</th>
                                        <th>Is Active?</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<!-- Close HTML -->


<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editModalTitle"><i class="fas fa-edit"></i> Edit <?= $viewTextify; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
            </div>
        </div>
    </div>
</div>
<script>
    const adminId = "<?php echo $_SESSION['admin_id']; ?>";
</script>
<?php
echo html_scripts(true, true, true, "assets/js/spur.js", true);
?>


<?php
$scripts = [
    'pages' => 'assets/js/pages.js',
    'admins' => 'assets/js/admins.js',
    'countries' => 'assets/js/countries.js',
];

if ($view === 'pages' || $view === 'admins' || $view === 'countries' && isset($scripts[$view])) {
    $script = $scripts[$view];
    $token = '?token=' . bin2hex(random_bytes(8));
    echo "<script src=\"{$script}{$token}\"></script>";
}


// Include DataTable script everywhere except for the 'country' page

if ($view === 'pages' || $view === 'admins') {
    echo '<script>
        $(document).ready(function() {
            // Use a timeout to delay DataTable initialization
            setTimeout(function() {
                // Initialize DataTable only if there are rows
                if ($("#data_table tbody tr").length > 0) {
                    var table = $("#data_table").DataTable({
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

<?php if ($view === 'admins') { ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editAdminRole');

            if (form) {
                form.addEventListener('submit', function(event) {
                    const checkboxes = form.querySelectorAll('input[name="adminRole[]"]:checked');
                    if (checkboxes.length === 0) {
                        event.preventDefault(); // Prevent form submission
                        alert('Please select at least one admin role.'); // Show error message
                    }
                });
            }
        });
    </script>
    <script>
        // Get all <td> elements with the class 'adminRole' (or any other identifier)
        let adminRoleCells = document.querySelectorAll('td.adminRole');

        // Loop through each <td> element
        adminRoleCells.forEach(cell => {
            // Get the content of the current <td> element
            let adminRole = cell.textContent;

            // Split the content by comma and trim any extra spaces
            let roles = adminRole.split(',').map(role => role.trim());

            // Capitalize the first letter of each role
            let formattedRoles = roles.map(role => role.charAt(0).toUpperCase() + role.slice(1)).join(', ');

            // Create a link with the formatted roles as the text
            let link = `<a href="#">${formattedRoles}</a>`;

            // Replace the content of the <td> element with the link
            cell.innerHTML = link;
        });
    </script>
<?php } ?>



</body>

</html>