<?php
if (isset($_GET['show'])) { // Check if 'show' parameter is set in the URL
    // Sanitize and validate the 'show' parameter
    $show = filter_var($_GET['show'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Remove harmful characters
    if (!empty($show)) { // Check if 'show' parameter is not empty
        // Validate the format of 'show' (allow only letters, numbers, underscores, and hyphens)
        if (preg_match('/^[a-zA-Z0-9_-]+$/', $show)) {
            $showTextify = ucwords(str_replace(['_', '-'], ' ', strip_tags(stripslashes($show))));
        } else {
            // Redirect to 404 if the 'show' parameter contains invalid characters
            header('Location: 404.php');
            exit;
        }
    } else {
        // Redirect to 404 if the 'show' parameter is empty
        header('Location: 404.php');
        exit;
    }
}
?>

<!-- Visa Type Table -->
<?php if ($show !== 'faq'): ?>
    <table id="recordTable" class="table table-bordered bg-white visa_table my-2">
        <thead class="table-dark">
            <tr>
                <th><?= $showTextify; ?></th>
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
                <th><?= $showTextify; ?></th>
                <th>Added by</th>
                <th>Added On</th>
                <th>Edited On</th>
                <th>Is Active?</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>
<?php else: ?>
    <table id="recordTable" class="table table-bordered bg-white faq_table my-2">
        <thead class="table-dark">
            <tr>
                <th>Question</th>
                <th>Answer</th>
                <th>Country</th>
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
                <th>Question</th>
                <th>Answer</th>
                <th>Country</th>
                <th>Added by</th>
                <th>Added On</th>
                <th>Edited On</th>
                <th>Is Active?</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>
<?php endif; ?>


<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content  rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle fw-bold"><i class="fas fa-plus-circle"></i> Add <?= $showTextify; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php include 'forms/add/' . $show . '.php'; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editModalTitle"><i class="fas fa-edit"></i> Edit Visa Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
            </div>
        </div>
    </div>
</div>