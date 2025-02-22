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
    " " . htmlspecialchars('Profile', ENT_QUOTES, 'UTF-8'), // Ensure safe output
    null,
    false, // Include Chart.js
    true // Include FontAwesome
);


// Fetch admin record
require 'Database.php';
$admin_id = $_SESSION['admin_id'];
$admin = $database->get("admins", "*", ["admin_id" => $admin_id]);
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
                    Profile
                </h1>
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h4 class="mb-0">My Profile</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Profile Photo -->
                                    <div class="col-md-2 text-center">
                                        <img id="photo-preview" src="https://www.htgtrading.co.uk/wp-content/uploads/2016/03/no-user-image-square-250x250.jpg"
                                            class="img-thumbnail mb-3" style="cursor: pointer; max-height: 200px;"
                                            alt="Profile Photo" onclick="triggerFileInput()">
                                        <input type="file" id="profile-photo" name="profile-photo" class="d-none" onchange="previewImage(event)" accept="image/*">
                                        <button class="btn btn-primary btn-sm mt-2 d-block mx-auto" onclick="triggerFileInput()">Change Photo</button>
                                    </div>

                                    <!-- Profile Details -->
                                    <div class="col-md-10">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th>Name:</th>
                                                <td><?= htmlspecialchars($admin['admin_name']); ?> <i class="bi bi-patch-check-fill text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <th>Role:</th>
                                                <td>
                                                    <?php
                                                    if (!empty($admin['admin_role'])) {
                                                        // Convert to array if it's a string
                                                        $roles = is_array($admin['admin_role']) ? $admin['admin_role'] : explode(',', $admin['admin_role']);
                                                        // Apply ucwords and join with commas
                                                        echo implode(', ', array_map('ucwords', $roles));
                                                    } else {
                                                        echo 'No roles assigned';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Department:</th>
                                                <td><?= htmlspecialchars($admin['admin_dept']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Join Date:</th>
                                                <td><?= (new DateTime($admin['created_at']))->format('F j, Y g:i A'); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Last Login:</th>
                                                <td><?= (new DateTime($admin['last_login_datetime']))->format('F j, Y g:i A'); ?></td>
                                            </tr>
                                            <tr>
                                                <th>IP Address:</th>
                                                <td><?= htmlspecialchars($admin['login_ip_address']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Location:</th>
                                                <td>
                                                    <?php
                                                    $location = json_decode($admin['login_location'], true);
                                                    if ($location) {
                                                        echo "City: {$location['city']}, Region: {$location['region']}, Country: {$location['country']}";
                                                    } else {
                                                        echo '<span class="text-muted">Location not available</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="settings.php" class="btn btn-secondary">Change Password</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>
</div>


<!-- Modal to view encryption password -->
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Enter Admin Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="card-text">To view the encryption password, you need to enter your admin password.</p>

                <p class="alert alert-info"> <i class="bi bi-info-circle"></i> The encryption password is required to access uploaded files.</p>
                <hr>
                <form id="passwordForm">
                    <div class="form-group">
                        <label for="adminPassword">Enter your password</label>
                        <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                    </div>
                    <div class="alert alert-danger d-none" id="errorMessage" role="alert">
                        Invalid password. Please try again.
                    </div>

                    <button type="button" class="btn btn-primary" id="submitPassword">Submit</button>
                </form>
            </div>
        </div>
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
    function triggerFileInput() {
        // Trigger the hidden file input when the image is clicked
        document.getElementById('profile-photo').click();
    }

    function previewImage(event) {
        const reader = new FileReader();
        const file = event.target.files[0];

        reader.onload = function() {
            const preview = document.getElementById('photo-preview');
            preview.src = reader.result; // Update image with new file
        }

        if (file) {
            reader.readAsDataURL(file); // Convert file to base64 to show preview
        }
    }
</script>

<script>
    // view encryption password
    $(document).ready(function() {
        $('#submitPassword').on('click', function() {
            const password = $('#adminPassword').val();
            const errorMessage = $('#errorMessage');

            if (!password) {
                errorMessage.text('Password cannot be empty.').removeClass('d-none');
                return;
            }

            // Send password to API
            $.ajax({
                url: 'api/v1/viewEncryptionPassword.php',
                method: 'POST',
                data: {
                    password: password
                },
                success: function(response) {
                    // Handle success
                    if (response.success) {
                        alert('Password accepted. Proceeding...');
                        $('#passwordModal').modal('hide');
                    } else {
                        errorMessage.text('Invalid password. Please try again.').removeClass('d-none');
                    }
                },
                error: function() {
                    // Handle error
                    errorMessage.text('An error occurred. Please try again later.').removeClass('d-none');
                }
            });
        });

        // Clear error message when modal is hidden
        $('#passwordModal').on('hidden.bs.modal', function() {
            $('#adminPassword').val('');
            $('#errorMessage').addClass('d-none');
        });
    });
</script>

<script>
    $(function() {
        $('[data-toggle="popover"]').popover({
            trigger: 'hover'
        });
    });
</script>


</body>

</html>