<?php

// Check if 'id' is present in the request
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Sanitize and retrieve the 'id' value
    $postId = intval($_GET['id']); // Use intval to ensure it's an integer

    // Fetch the data based on the post_id using Medoo
    $page = $database->get("pages", "*", [
        "id" => $postId
    ]);

    // Check if data was returned
    if (empty($page)) {
        echo json_encode(["message" => "No data found for the given Post ID."]);
        exit;
    }
} else {
    echo json_encode(["message" => "Post ID is missing."]);
    exit;
}
?>
<div class="card">
    <div class="card-header h5 font-weight-bold">
        Editing page: <span class="text-info"><?= htmlspecialchars($page['pageName']); ?></span>
    </div>
    <div class="card-body">
        <form id="editpageForm" novalidate>
            <div class="row">
                <!-- Page Name -->
                <div class="col-12 form-group">
                    <label for="pageName">Page Name</label>
                    <input type="text" class="form-control" id="pageName" name="pageName" placeholder="Enter page name" value="<?= htmlspecialchars($page['pageName']); ?>" required>
                    <div class="invalid-feedback">
                        Please enter a page name
                    </div>
                </div>


                <!-- Page Slug -->
                <div class="col-12 form-group">
                    <label for="pageDetails">Slug</label>
                    <!-- <textarea class="form-control" id="pageSlug" name="pageSlug" rows="4" placeholder="Enter page details"></textarea> -->
                    <input type="text" class="form-control" name="pageSlug" id="pageSlug" value="<?= htmlspecialchars($page['pageSlug']); ?>" required>
                </div>

                <!-- Page Description -->
                <div class="col-12 form-group">
                    <label for="pageDescription">Page Description</label>
                    <textarea class="form-control" id="pageDescription" name="pageDescription" placeholder="Enter page description" required><?= htmlspecialchars($page['pageDescription']); ?></textarea>
                    <div class="invalid-feedback">
                        Please enter a page description
                    </div>
                </div>
                <!-- Page Details -->
                <div class="col-12 form-group">
                    <label for="pageDetails">Page Content</label>
                    <textarea class="form-control" id="pageDetails" name="pageDetails" rows="4" placeholder="Enter page details"><?= htmlspecialchars($page['pageDetails']); ?></textarea>
                </div>



                <!-- Page Position -->
                <div class="col-12 form-group">
                    <label for="pagePosition">Page Position</label>
                    <select class="form-control" id="pagePosition" name="pagePosition" required>
                        <option value="" disabled>Select position</option>
                        <option value="header" <?= $page['pagePosition'] === 'header' ? 'selected' : ''; ?>>Header</option>
                        <option value="footer" <?= $page['pagePosition'] === 'footer' ? 'selected' : ''; ?>>Footer</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select a page position
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="row">
                <div class="col-12 text-left">
                    <input type="hidden" id="admin_id" name="admin_id" value="<?= $_SESSION['admin_id']; ?>">
                    <input type="hidden" id="pageId" name="page_id" value="<?= $postId; ?>">
                    <button type="submit" class="btn btn-primary">Update Page</button>
                </div>
            </div>
        </form>
    </div>
</div>