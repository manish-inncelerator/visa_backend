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
    " " . htmlspecialchars('Notifications', ENT_QUOTES, 'UTF-8'), // Ensure safe output
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
                    Notifications
                </h1>
                <div class="row">
                    <div class="col-12">
                        <!-- Notifications Card -->
                        <div class="card">
                            <div class="card-header h5 font-weight-bold">
                                Notification Center
                            </div>
                            <div class="card-body">


                                <!-- Tabs Navigation -->
                                <ul class="nav nav-tabs" id="notificationsTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="unread-tab" data-toggle="tab" href="#unread" role="tab" aria-controls="unread" aria-selected="true">
                                            Unread
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="read-tab" data-toggle="tab" href="#read" role="tab" aria-controls="read" aria-selected="false">
                                            Read
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tabs Content -->
                                <div class="tab-content mt-3" id="notificationsTabsContent">
                                    <!-- Unread Notifications Tab -->
                                    <div class="tab-pane fade show active" id="unread" role="tabpanel" aria-labelledby="unread-tab">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-start" style="background-color:bisque;">
                                                <div>
                                                    <a href="#" class="text-decoration-none font-weight-bold">New comment on your post</a>
                                                    <p class="mb-0 small text-muted">Someone commented on your latest post.</p>
                                                </div>
                                                <span class="text-muted small">5 mins ago</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-start" style="background-color: bisque;">
                                                <div>
                                                    <a href="#" class="text-decoration-none font-weight-bold">Profile update required</a>
                                                    <p class="mb-0 small text-muted">Your profile is incomplete. Update now.</p>
                                                </div>
                                                <span class="text-muted small">30 mins ago</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Read Notifications Tab -->
                                    <div class="tab-pane fade" id="read" role="tabpanel" aria-labelledby="read-tab">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                <div>
                                                    <a href="#" class="text-decoration-none">Your password was changed successfully</a>
                                                    <p class="mb-0 small text-muted">You updated your password yesterday.</p>
                                                </div>
                                                <span class="text-muted small">1 day ago</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                <div>
                                                    <a href="#" class="text-decoration-none">Welcome to our platform!</a>
                                                    <p class="mb-0 small text-muted">We're excited to have you on board.</p>
                                                </div>
                                                <span class="text-muted small">3 days ago</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ./Notifications Card -->

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

</body>

</html>