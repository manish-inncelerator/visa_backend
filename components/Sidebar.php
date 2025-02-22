<?php
// Require database connection
require 'Database.php';

// Fetch admin record
$admin_id = $_SESSION['admin_id'];
$admin = $database->get("admins", "*", ["admin_id" => $admin_id]);
$admin_role = $admin['admin_role']; // Get the admin role (comma-separated string)

// Split the admin roles into an array
$admin_roles = explode(',', $admin_role);

// Define the navigation links as an array
$navLinks = [];

function renderNav($navLinks, $admin_roles)
{
    // Get the current page name and query string parameters from the URL
    $currentPage = basename($_SERVER['PHP_SELF']); // This gets the current page's filename
    $queryParams = $_GET; // This gets all query parameters in the URL

    foreach ($navLinks as $item) {
        // Check if the item is visible for the current role(s)
        if (isset($item['visibility'])) {
            $isVisible = false;
            foreach ($admin_roles as $role) {
                if (in_array($role, $item['visibility'])) {
                    $isVisible = true;
                    break;
                }
            }
            if (!$isVisible) {
                continue; // Skip rendering this item if the role is not allowed
            }
        }

        // Check if the item is a subheading and should be displayed
        if (isset($item['isHeading']) && $item['isHeading']) {
            // Render the subheading with the additional classes
            echo '<div class="dash-nav-heading ml-2 text-green fw-bold">' . $item['title'] . '</div>';
        } elseif (!isset($item['isHeading'])) {
            // Check if the item has a dropdown
            if (isset($item['dropdown']) && $item['dropdown']) {
                // Initialize the class for the dropdown
                $dropdownActive = '';

                // Check if any item in the dropdown is active
                foreach ($item['dropdown'] as $dropdownItem) {
                    // Match if the link or query parameter matches the current page or parameters
                    if (
                        basename($dropdownItem['link']) == $currentPage ||
                        (isset($_GET['add']) && strpos($dropdownItem['link'], 'add=' . $_GET['add']) !== false) ||
                        (isset($_GET['view']) && strpos($dropdownItem['link'], 'view=' . $_GET['view']) !== false)
                    ) {
                        $dropdownActive = ' show'; // Make the dropdown open
                        break;
                    }
                }

                // Output the dropdown, add the 'show' class if it has an active link
                echo '<div class="dash-nav-dropdown' . $dropdownActive . '">';
                echo '<a href="#" class="dash-nav-item dash-nav-dropdown-toggle ">';
                echo '<i class="bi ' . $item['icon'] . '"></i> ' . $item['title'];
                echo '</a>';
                echo '<div class="dash-nav-dropdown-menu">';
                // Render the items inside the dropdown recursively
                renderNav($item['dropdown'], $admin_roles);
                echo '</div>';
                echo '</div>';
            } else {
                // Initialize activeClass as empty
                $activeClass = '';

                // Check if the link matches the current page (file name)
                if (basename($item['link']) == $currentPage) {
                    $activeClass = ' active_link';
                }

                // Check if the URL contains specific parameters (e.g., add or view) and match with the title
                if (isset($item['link']) && strpos($item['link'], '?') !== false) {
                    // Extract the query string from the URL in the item
                    $urlParams = parse_url($item['link'], PHP_URL_QUERY);
                    parse_str($urlParams, $params);

                    // Check if the current URL has the same 'add' parameter as the item title
                    if (isset($params['add']) && isset($queryParams['add']) && $params['add'] == $queryParams['add']) {
                        $activeClass = ' active_link';
                    }

                    // Check if the current URL has the same 'view' parameter as the item title
                    if (isset($params['view']) && isset($queryParams['view']) && $params['view'] == $queryParams['view']) {
                        $activeClass = ' active_link';
                    }
                }

                // If the link is the logout page, set the target attribute
                $target = isset($item['external']) && $item['external'] ? ' target="_blank"' : '';

                // Output the <a> tag with the proper active class
                echo '<a href="' . $item['link'] . '" class="dash-nav-item  ' . ($item['link'] === 'logout.php' ? 'text-danger' : '') . $activeClass . '"' . $target . '>';

                if (isset($item['icon'])) {
                    echo '<i class="bi ' . $item['icon'] . '"></i> ';
                }

                echo $item['title'] . '</a>';
            }
        }
    }
}

$navLinks = [
    [
        'title' => 'Main Menu',
        'isHeading' => true,
        'visibility' => ['master'], // All roles can see this
    ],
    [
        'title' => 'Dashboard',
        'icon' => 'bi-house-door',
        'link' => 'dashboard.php',
        'visibility' => ['master'], // All roles can see this
    ],
    [
        'title' => 'Orders <span class="badge badge-danger rounded-pill ml-1 my-1">0</span>',
        'icon' => 'bi-cart',
        'dropdown' => [
            ['title' => 'New', 'link' => 'view.php?view=visa_new_applications'],
            ['title' => 'In processing', 'link' => 'view.php?view=visa_in_processing'],
            ['title' => 'Rejected', 'link' => 'view.php?view=visa_rejected_applications'],
            ['title' => 'Approved', 'link' => 'view.php?view=visa_approved'],
            ['title' => 'Withdrawn', 'link' => 'view.php?view=visa_withdrawn'],
        ],
        'visibility' => ['master', 'visa associate', 'visa consultant'], // Only master and visa associate can see this
    ],
    [
        'title' => 'Reports',
        'icon' => 'bi-bar-chart',
        'link' => 'reports.html',
        'visibility' => ['master', 'accountant'], // All roles can see this
    ],
    [
        'title' => 'Tickets <span class="badge badge-danger rounded-pill ml-1 my-1">0</span>',
        'icon' => 'bi-ticket',
        'link' => 'tickets.php',
        'visibility' => ['visa associate', 'visa consultant', 'master'], // All roles can see this
    ],
    [
        'title' => 'Admins',
        'icon' => 'bi-people',
        'link' => 'view.php?view=admins',
        'visibility' => ['master'], // Only master can see this
    ],
    [
        'title' => 'Transactions',
        'icon' => 'bi-cash-coin',
        'link' => 'view.php?view=transactions',
        'visibility' => ['master', 'accountant'], // Only master and accountant can see this
    ],
    [
        'title' => 'Countries & Pages',
        'isHeading' => true,
        'visibility' => ['editor'], // Only editor can see this
    ],
    [
        'title' => 'Countries',
        'icon' => 'bi-globe',
        'dropdown' => [
            ['title' => 'Add New', 'link' => 'add.php?add=countries'],
            ['title' => 'View All', 'link' => 'view.php?view=countries'],
        ],
        'visibility' => ['editor'], // Only editor can see this
    ],
    [
        'title' => 'Pages',
        'icon' => 'bi-file-earmark',
        'dropdown' => [
            ['title' => 'Add New', 'link' => 'add.php?add=pages'],
            ['title' => 'View All', 'link' => 'view.php?view=pages'],
        ],
        'visibility' => ['editor'], // Only editor can see this
    ],
    [
        'title' => 'Visa',
        'isHeading' => true,
        'visibility' => ['editor'], // Only editor can see this
    ],
    [
        'title' => 'Types & Kinds',
        'icon' => 'bi-folder',
        'dropdown' => [
            ['title' => 'Visa Type', 'link' => 'add.php?add=visa_type'],
            ['title' => 'Visa Kind', 'link' => 'add.php?add=visa_kind'],
            ['title' => 'Visa Category', 'link' => 'add.php?add=visa_category']
        ],
        'visibility' => ['editor'], // Only editor can see this
    ],
    [
        'title' => 'Required Documents',
        'isHeading' => true,
        'visibility' => ['editor'], // Only editor can see this
    ],
    [
        'title' => 'Documents',
        'icon' => 'bi-files',
        'link' => 'add.php?add=required_documents',
        'visibility' => ['editor'], // Only editor can see this
    ],
    [
        'title' => 'FAQ',
        'isHeading' => true,
        'visibility' => ['editor'], // Only editor can see this
    ],
    [
        'title' => 'FAQ',
        'icon' => 'bi-patch-question',
        'link' => 'add.php?add=faq',
        'visibility' => ['editor'], // Only editor can see this
    ],
    [
        'title' => 'Others',
        'isHeading' => true,
        'visibility' => ['master', 'accountant', 'editor', 'visa associate'], // All roles can see this
    ],
    [
        'title' => 'Notifications <span class="badge badge-danger rounded-pill ml-1 my-1">0</span>',
        'icon' => 'bi-bell',
        'link' => 'notifications.php',
        'visibility' => ['master', 'accountant', 'editor', 'visa associate'], // All roles can see this
    ],
    [
        'title' => 'Settings',
        'icon' => 'bi-gear',
        'link' => 'settings.php',
        'visibility' => ['master', 'accountant', 'editor', 'visa associate'], // All roles can see this
    ],
    [
        'title' => 'Profile',
        'icon' => 'bi-person',
        'link' => 'profile.php',
        'visibility' => ['master', 'accountant', 'editor', 'visa associate'], // All roles can see this
    ],
    [
        'title' => 'Logout',
        'icon' => 'bi-box-arrow-right',
        'link' => 'logout.php',
        'visibility' => ['master', 'accountant', 'editor', 'visa associate'], // All roles can see this
        'external' => false, // Open in a new tab
    ]
];
?>
<div class="sidebar">
    <div class="sidebar-logo text-center p-3">
        <a href="dashboard.php">
            <img src="assets/img/main-logo-white.png" alt="Logo" class="img-fluid" style="max-width: 100px;">
        </a>
    </div>
    <?php
    // Render the navigation menu
    renderNav($navLinks, $admin_roles);
    ?>
</div>