<?php
function html_scripts(
    $includeJQuery = true,
    $includePopper = true,
    $includeBootstrap = true,
    $customScript = "assets/js/spur.js",
    $includeSwal = false,
) {
    // Conditionally include jQuery
    $jQuery = $includeJQuery ? '<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>' : '';

    // Conditionally include Popper.js
    $popper = $includePopper ? '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" crossorigin="anonymous"></script>' : '';

    // Conditionally include Bootstrap JS
    $bootstrap = $includeBootstrap ? '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>' : '';

    // Custom script
    $custom = $customScript ? "<script src=\"$customScript\"></script>" : '';

    // Sweet alert 2
    $swal = $includeSwal ? '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>' : '';

    function includeScript($condition, $scripts)
    {
        if ($condition) {
            $token = '?token=' . bin2hex(random_bytes(8));
            return implode("\n", array_map(fn($script) => "<script src=\"{$script}{$token}\"></script>", $scripts));
        }
        return '';
    }

    $page = basename($_SERVER['PHP_SELF']);
    $add = $_GET['add'] ?? '';
    $view = $_GET['view'] ?? '';

    // Include Choices.js for ?add=country
    $choicesJs = includeScript(
        in_array($page, ['add.php', 'edit.php']) && $add === 'countries',
        ['https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js']
    );
    $dataTablesJs = includeScript(
        in_array($page, ['add.php', 'edit.php', 'view.php']) &&
            (
                in_array($add, ['visa_type', 'visa_kind', 'required_documents', 'faq', 'admins', 'visa_category']) ||
                (isset($view) && in_array($view, ['pages', 'admins']))
            ),
        [
            'https://cdn.datatables.net/2.2.1/js/dataTables.min.js',
            'https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap4.min.js'
        ]
    );


    return <<<HTML
    {$jQuery}
    {$popper}
    {$bootstrap}
    {$custom}
    {$swal}
    {$choicesJs}
    {$dataTablesJs}
    HTML;
}
