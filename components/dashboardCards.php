<?php
require 'Database.php';

$email = $_SESSION['admin_email_address'];
$user = $database->get(
    'admins',
    [
        'admin_role',
    ],
    [
        'AND' => [
            'admin_email_address' => $email,
            'is_active' => 1 // Assuming 1 means active
        ]
    ]
);

$role = trim(stripslashes(strip_tags($_GET['role'])));
$adminRoles = explode(',', $user['admin_role']); // Split admin roles into an array

// Check if the user has access to the requested role
if (!$role === 'visa') {
    if (!in_array($role, $adminRoles)) {
        echo "You are not authorized to view this page.";
        exit;
    }
}

// Define all cards and their visibility for each role
$allCards = [
    // Revenue Section (Orange Colors)
    [
        'title' => 'Revenue Today',
        'value' => 'SGD 11.61k',
        'icon' => 'bi-currency-dollar',
        'bgColor' => 'l-bg-orange-dark', // Same color for all Revenue cards
        'percentage' => '2.5%',
        'visible_to' => ['master', 'accountant']
    ],
    [
        'title' => 'Revenue Yesterday',
        'value' => 'SGD 10.45k',
        'icon' => 'bi-currency-dollar',
        'bgColor' => 'l-bg-orange-dark', // Same color for all Revenue cards
        'percentage' => '1.8%',
        'visible_to' => ['master', 'accountant']
    ],
    [
        'title' => 'Revenue This Week',
        'value' => 'SGD 75.30k',
        'icon' => 'bi-currency-dollar',
        'bgColor' => 'l-bg-orange-dark', // Same color for all Revenue cards
        'percentage' => '5.2%',
        'visible_to' => ['master', 'accountant']
    ],
    [
        'title' => 'Revenue This Month',
        'value' => 'SGD 320.15k',
        'icon' => 'bi-currency-dollar',
        'bgColor' => 'l-bg-orange-dark', // Same color for all Revenue cards
        'percentage' => '8.7%',
        'visible_to' => ['master', 'accountant']
    ],

    // Visa Section (Blue Colors)
    [
        'title' => 'New Visa Orders',
        'value' => '1,200',
        'icon' => 'bi-passport',
        'bgColor' => 'l-bg-blue-dark', // Same color for all Visa cards
        'percentage' => '8.5%',
        'visible_to' => ['visa associate', 'visa consultant']
    ],
    [
        'title' => 'Visa Approved',
        'value' => '950',
        'icon' => 'bi-check-circle',
        'bgColor' => 'l-bg-blue-dark', // Same color for all Visa cards
        'percentage' => '10%',
        'visible_to' => ['visa associate', 'visa consultant']
    ],
    [
        'title' => 'Visa Rejected',
        'value' => '150',
        'icon' => 'bi-x-circle',
        'bgColor' => 'l-bg-blue-dark', // Same color for all Visa cards
        'percentage' => '5%',
        'visible_to' => ['visa associate', 'visa consultant']
    ],
    [
        'title' => 'Visa Withdrawn',
        'value' => '75',
        'icon' => 'bi-arrow-return-left',
        'bgColor' => 'l-bg-blue-dark', // Same color for all Visa cards
        'percentage' => '3%',
        'visible_to' => ['visa associate', 'visa consultant']
    ],

    // Tickets Section (Green Colors)
    [
        'title' => 'New Tickets',
        'value' => '120',
        'icon' => 'bi-ticket',
        'bgColor' => 'l-bg-green-dark', // Same color for all Tickets cards
        'percentage' => '15%',
        'visible_to' => ['master', 'accountant', 'visa consultant', 'visa associate']
    ],
    [
        'title' => 'Unresolved Tickets',
        'value' => '45',
        'icon' => 'bi-hourglass',
        'bgColor' => 'l-bg-green-dark', // Same color for all Tickets cards
        'percentage' => '10%',
        'visible_to' => ['master', 'accountant', 'visa consultant', 'visa associate']
    ],
    [
        'title' => 'Resolved Tickets by Bot',
        'value' => '45',
        'icon' => 'bi-check-circle',
        'bgColor' => 'l-bg-green-dark', // Same color for all Tickets cards
        'percentage' => '10%',
        'visible_to' => ['master', 'accountant', 'visa consultant', 'visa associate']
    ],
    [
        'title' => 'Resolved Tickets',
        'value' => '75',
        'icon' => 'bi-check-circle',
        'bgColor' => 'l-bg-green-dark', // Same color for all Tickets cards
        'percentage' => '20%',
        'visible_to' => ['master', 'accountant', 'visa consultant', 'visa associate']
    ],

    // Customers Section (Purple Colors)
    [
        'title' => 'Customers',
        'value' => '15.07k',
        'icon' => 'bi-people',
        'bgColor' => 'l-bg-purple-dark', // Same color for all Customers cards
        'percentage' => '9.23%',
        'visible_to' => ['master', 'accountant', 'visa associate', 'visa consultant', 'visa consultant', 'visa associate']
    ],
    [
        'title' => 'New Customers Today',
        'value' => '320',
        'icon' => 'bi-person-plus',
        'bgColor' => 'l-bg-purple-dark', // Same color for all Customers cards
        'percentage' => '12%',
        'visible_to' => ['master', 'accountant', 'visa associate', 'visa consultant', 'visa consultant', 'visa associate']
    ],
    [
        'title' => 'New Customers Yesterday',
        'value' => '280',
        'icon' => 'bi-person-plus',
        'bgColor' => 'l-bg-purple-dark', // Same color for all Customers cards
        'percentage' => '8%',
        'visible_to' => ['master', 'accountant', 'visa associate', 'visa consultant', 'visa consultant', 'visa associate']
    ],
    [
        'title' => 'New Customers This Week',
        'value' => '1,500',
        'icon' => 'bi-person-plus',
        'bgColor' => 'l-bg-purple-dark', // Same color for all Customers cards
        'percentage' => '15%',
        'visible_to' => ['master', 'accountant', 'visa associate', 'visa consultant', 'visa consultant', 'visa associate']
    ],
    [
        'title' => 'New Customers This Month',
        'value' => '6,200',
        'icon' => 'bi-person-plus',
        'bgColor' => 'l-bg-purple-dark', // Same color for all Customers cards
        'percentage' => '18%',
        'visible_to' => ['master', 'accountant', 'visa associate', 'visa consultant', 'visa consultant', 'visa associate']
    ]
];

// Filter cards based on the admin roles
$filteredCards = array_filter($allCards, function ($card) use ($adminRoles) {
    // Check if any of the user's roles match the card's visible_to roles
    return count(array_intersect($adminRoles, $card['visible_to'])) > 0;
});
?>

<div class="row">
    <?php foreach ($filteredCards as $card): ?>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card c <?php echo $card['bgColor']; ?>">
                <div class="card-statistic-3 p-4">
                    <div class="card-icon card-icon-large"><i class="bi <?php echo $card['icon']; ?>"></i></div>
                    <div class="mb-4">
                        <h5 class="card-title mb-0"><?php echo $card['title']; ?></h5>
                    </div>
                    <div class="row align-items-center mb-2 d-flex">
                        <div class="col-8">
                            <h1 class="d-flex align-items-center mb-0 fw-bold">
                                <?php echo $card['value']; ?>
                            </h1>
                        </div>
                        <div class="col-4 text-right">
                            <span><?php echo $card['percentage']; ?> <i class="bi bi-arrow-up"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row my-3">
    <div class="col-12 col-xl-6 col-xxl-6">
        <div class="card">
            <div class="card-header">Revenue</div>
            <div class="card-body">
                <div class="card-text">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6 col-xxl-6">
        <div class="card">
            <div class="card-header">Revenue</div>
            <div class="card-body">
                <div class="card-text">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>