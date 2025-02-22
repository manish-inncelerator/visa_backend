<?php
function html_head(
    $title = "Portify",
    $bodyBgColor = "",
    $includeCharts = false,
    $includeFontAwesome = false
) {
    // Conditionally include Chart.js and FontAwesome
    $chartJs = $includeCharts ? '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script><script src="assets/js/chart-js-config.js"></script>' : '';

    $fontAwesome = $includeFontAwesome ? '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">' : '';

    // Conditionally set body class
    $bodyClass = $bodyBgColor ? "class=\"$bodyBgColor\"" : "";

    function includeStyles($condition, $styles)
    {
        if ($condition) {
            return implode("\n", array_map(fn($style) => "<link rel=\"stylesheet\" href=\"{$style}\">", $styles));
        }
        return '';
    }

    $page = basename($_SERVER['PHP_SELF']);
    $add = $_GET['add'] ?? '';
    $view = $_GET['view'] ?? '';

    // Include Choices.js CSS for ?add=countries or ?view={anything}
    $choicesCss = includeStyles(
        (in_array($page, ['add.php', 'edit.php']) && $add === 'countries'),
        ['https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css']
    );


    // Include DataTables CSS for ?add=visa_type or ?add=visa_kind
    $dataTableCss = includeStyles(
        (in_array($page, ['add.php', 'edit.php']) && in_array($add, ['visa_type', 'visa_type', 'visa_kind', 'required_documents', 'faq', 'admins'])) || in_array($view, ['pages', 'admins']),
        [
            'https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap4.min.css'
        ]
    );

    $tinymceIncludes = includeStyles(
        $page === 'pages',
        '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tinymce@5.1.0/skins/ui/oxide/content.min.css">'
    );

    $token = bin2hex(random_bytes(8));

    // Preconnect and preload links

    $preconnectLinks = '
        <link rel="preconnect" href="https://cdn.datatables.net" />
        <link rel="dns-prefetch" href="https://cdn.datatables.net" />
        <link rel="preconnect" href="https://cdnjs.cloudflare.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link rel="preconnect" href="https://cdn.jsdelivr.net" />

        <link rel="preload" href="assets/css/spur.css" as="style" />
        <link rel="preload" href="assets/favicon/favicon-96x96.png" as="image" />
        <link rel="preload" href="assets/favicon/favicon.svg" as="image" />
        <link rel="preload" href="assets/favicon/favicon.ico" as="image" />
        <link rel="preload" href="assets/favicon/apple-touch-icon.png" as="image" />
        ';

    return <<<HTML
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, shrink-to-fit=no, use-scalable=no" />

            {$preconnectLinks}
            {$fontAwesome}
            <link href="https://fonts.googleapis.com/css?family=Nunito:400,600|Open+Sans:400,600,700" rel="stylesheet" />
            <link rel="icon" type="image/png" href="assets/favicon/favicon-96x96.png" sizes="96x96" />
            <link rel="icon" type="image/svg+xml" href="assets/favicon/favicon.svg" />
            <link rel="shortcut icon" href="assets/favicon/favicon.ico" />
            <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png" />
            <meta name="apple-mobile-web-app-title" content="Portify" />
            <link rel="stylesheet" href="assets/css/spur.min.css" />
            <link rel="stylesheet" href="assets/css/custom.css?token=$token" />
            <link rel="manifest" href="/site.webmanifest" />
            {$choicesCss}
            {$dataTableCss}
            {$chartJs}
            {$tinymceIncludes}
            <title>{$title} | Portify</title>
        </head>

        <body {$bodyClass}>
        HTML;
}
