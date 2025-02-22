<a href="#!" class="menu-toggle">
    <i class="bi bi-list"></i>
</a>
<a href="#!" class="searchbox-toggle">
    <i class="bi bi-search"></i>
</a>
<form class="searchbox" action="search.php">
    <a href="#!" class="searchbox-toggle">
        <i class="bi bi-arrow-left"></i>
    </a>
    <button type="submit" class="searchbox-submit">
        <i class="bi bi-search"></i>
    </button>
    <input
        type="text"
        class="searchbox-input" name="q"
        placeholder="type to search" required />
</form>

<div class="tools d-flex align-items-center">

    <!-- Notification Icon (With Dropdown) -->
    <div class="tools-item position-relative">
        <a href="#!" class="d-flex align-items-center" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="bi bi-bell"></i>
            <!-- Notification Indicator -->
            <span class="notification-indicator position-absolute top-0 start-100 translate-middle rounded-circle bg-danger" style="width: 8px; height: 8px;"></span>
        </a>
        <!-- Dropdown Menu -->
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown" style="width:600px;">
            <div class="dropdown-header">
                <strong>Notifications</strong>
            </div>
            <div class="dropdown-divider"></div>
            <!-- Notification Items -->
            <a class="dropdown-item d-flex align-items-center" href="#" role="menuitem">
                <i class="bi bi-bell-fill text-primary mr-3"></i>
                <div>
                    <div>New notification 1</div>
                    <small class="text-muted">5 minutes ago</small>
                </div>
            </a>
            <a class="dropdown-item d-flex align-items-center" href="#" role="menuitem">
                <i class="bi bi-bell-fill text-primary mr-3"></i>
                <div>
                    <div>New notification 2</div>
                    <small class="text-muted">10 minutes ago</small>
                </div>
            </a>
            <a class="dropdown-item d-flex align-items-center" href="#" role="menuitem">
                <i class="bi bi-bell-fill text-primary mr-3"></i>
                <div>
                    <div>New notification 3</div>
                    <small class="text-muted">30 minutes ago</small>
                </div>
            </a>
            <a class="dropdown-item d-flex align-items-center" href="#" role="menuitem">
                <i class="bi bi-bell-fill text-primary mr-3"></i>
                <div>
                    <div>New notification 4</div>
                    <small class="text-muted">1 hour ago</small>
                </div>
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-center" href="notifications.php">See all notifications</a>
        </div>
    </div>

    <!-- User Icon with Dropdown -->
    <div class="dropdown tools-item">
        <a
            href="#"
            class="d-flex align-items-center"
            id="dropdownMenu1"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
            <i class="bi bi-person"></i>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
            <!-- Disabled item with a visual style -->
            <a class="dropdown-item text-muted" href="#" style="pointer-events: none;">
                Manish Shahi <span class="text-success"><i class="bi bi-patch-check-fill"></i></span>
            </a>
            <span class="dropdown-divider"></span>

            <!-- Profile item with an icon -->
            <a class="dropdown-item" href="profile.php">
                <i class="bi bi-person"></i> Profile
            </a>

            <!-- Settings item with an icon -->
            <a class="dropdown-item" href="settings.php">
                <i class="bi bi-gear"></i> Settings
            </a>

            <!-- Logout item with an icon -->
            <a class="dropdown-item text-danger" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> Log out
            </a>
        </div>


    </div>
</div>