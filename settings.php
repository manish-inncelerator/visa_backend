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
echo html_head(
    " " . htmlspecialchars('Settings', ENT_QUOTES, 'UTF-8'), // Ensure safe output
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
                    Settings
                </h1>
                <div class="row">
                    <div class="col-12">
                        <!-- Settings Card -->
                        <div class="card">
                            <div class="card-header h5 font-weight-bold">
                                Edit Settings
                            </div>
                            <div class="card-body">

                                <p class="font-weight-bold text-muted mb-3">Focus Mode <span class="badge badge-danger">Under Development</span> <br> <small>Prevent notifications and chat messages.</small></p>
                                <!-- <p class="card-text" diabled><a href="" class="btn btn-success btn-disbaled">Turn On</a></p> -->



                                <!-- Change Password Section -->
                                <p class="font-weight-bold text-muted mb-3">Change Password</p>
                                <form id="changePasswordForm" novalidate>
                                    <div class="mb-3 row">
                                        <label for="currentPassword" class="col-sm-2 col-form-label">Current Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password" required>
                                            <div class="invalid-feedback">
                                                Please enter your current password.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="newPassword" class="col-sm-2 col-form-label">New Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="newPassword" placeholder="Enter new password" required>
                                            <div class="invalid-feedback">
                                                Please enter a new password.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="repeatPassword" class="col-sm-2 col-form-label">Repeat New Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="repeatPassword" placeholder="Repeat new password" required>
                                            <div class="invalid-feedback">
                                                Please repeat your new password.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 offset-sm-2">
                                            <button type="submit" class="btn btn-primary">Change Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- ./Settings Card -->
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    const adminId = "<?php echo $_SESSION['admin_id']; ?>";
</script>
<!-- Close HTML -->
<?php
echo html_scripts(true, true, true, "assets/js/spur.js", true);
?>
<script>
    document.getElementById('changePasswordForm').addEventListener('submit', async (event) => {
        event.preventDefault();

        const form = event.target;
        const newPassword = document.getElementById('newPassword').value;
        const repeatPassword = document.getElementById('repeatPassword').value;

        form.classList.add('was-validated');

        if (newPassword !== repeatPassword) {
            document.getElementById('repeatPassword').setCustomValidity("Passwords don't match.");
            return;
        }

        if (!form.checkValidity()) return;

        try {
            const response = await fetch('api/v1/change_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    currentPassword: document.getElementById('currentPassword').value,
                    newPassword: newPassword
                })
            });
            const data = await response.json();

            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? 'Success!' : 'Error',
                text: data.success ? 'Password changed successfully!' : `Error: ${data.message}`,
                confirmButtonText: 'OK'
            });
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while changing the password.',
                confirmButtonText: 'OK'
            });
        }
    });
</script>

</body>

</html>