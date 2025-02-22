<?php
// Start the session
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to the login page if the user is not logged in
    header('Location: login.html');
    exit;
}

// Define valid roles
$validRoles = ['master', 'visa consultant', 'visa associate', 'visa', 'editor', 'accountant'];

// Function to determine the highest priority role from a list of roles
function getHighestPriorityRole($roles)
{
    foreach (['master', 'visa consultant', 'visa associate', 'visa', 'editor', 'accountant'] as $priorityRole) {
        if (in_array($priorityRole, $roles)) {
            return $priorityRole;
        }
    }
    return null;
}

// Handle role redirection
if (isset($_GET['role']) || isset($_SESSION['admin_role'])) {
    $roles = isset($_GET['role']) ? explode(',', $_GET['role']) : explode(',', $_SESSION['admin_role']);
    $roles = array_map('trim', $roles); // Trim whitespace from each role

    // Validate roles
    foreach ($roles as $role) {
        if (!in_array($role, $validRoles)) {
            die("Invalid role."); // Stop execution and display an error message
        }
    }

    // Get the highest priority role
    $highestPriorityRole = getHighestPriorityRole($roles);

    // Redirect if the highest priority role is found and the current role is not already correct
    if ($highestPriorityRole && (!isset($_GET['role']) || $_GET['role'] !== $highestPriorityRole)) {
        header('Location: dashboard.php?role=' . $highestPriorityRole);
        exit;
    }
}

// Include HTML header and footer
include 'inc/html_head.php';
include 'inc/html_foot.php';

// Render the HTML head
echo html_head(
    "Dashboard",
    null,
    false, // Include Chart.js
    true   // Include FontAwesome
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
                    Dashboard
                </h1>
                <?php require 'components/dashboardCards.php'; ?>
            </div>
        </main>
    </div>
</div>

<!-- Pass admin ID to JavaScript -->
<script>
    const adminId = "<?php echo $_SESSION['admin_id']; ?>";
</script>

<?php
// Render HTML scripts
echo html_scripts(true, true, true, "assets/js/spur.js", false);
?>
</body>

</html>