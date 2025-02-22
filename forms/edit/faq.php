<?php

session_start();
// Connect to the database
require '../../Database.php';

// Check if 'post_id' is present in the request
if (isset($_GET['post_id']) && !empty($_GET['post_id'])) {
    // Sanitize and retrieve the 'post_id' value
    $postId = intval($_GET['post_id']); // Use intval to ensure it's an integer

    // Fetch the data based on the post_id using Medoo
    $faqs = $database->select("faq", "*", [
        "id" => $postId
    ]);

    // Check if data was returned
    if (empty($faqs)) {
        echo json_encode(["message" => "No data found for the given Post ID."]);
        exit;
    }

    // Display form for each FAQ
    foreach ($faqs as $faq) {
?>
        <form id="editfaqForm" novalidate>
            <!-- FAQ Fields -->
            <div class="form-group">
                <label for="faqQuestion">FAQ Question</label>
                <input type="text" class="form-control" id="faqQuestion" name="faqQuestion"
                    value="<?= htmlspecialchars($faq['faqQuestion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required />
                <div class="invalid-feedback">
                    Please enter a question.
                </div>
            </div>

            <div class="form-group">
                <label for="faqAnswer">FAQ Answer</label>
                <textarea class="form-control" id="faqAnswer" name="faqAnswer" rows="3" placeholder="Enter FAQ answer" required><?= htmlspecialchars($faq['faqAnswer'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <div class="invalid-feedback">
                    Please enter an answer.
                </div>
            </div>

            <!-- Country Select Box -->
            <div class="form-group">
                <label for="faqCountry">Country</label>
                <select class="form-control" id="faqCountry" name="faqCountry" required>
                    <option value="USA" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'USA' ? 'selected' : ''; ?>>United States</option>
                    <option value="Canada" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'Canada' ? 'selected' : ''; ?>>Canada</option>
                    <option value="UK" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'UK' ? 'selected' : ''; ?>>United Kingdom</option>
                    <option value="India" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'India' ? 'selected' : ''; ?>>India</option>
                    <option value="Australia" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'Australia' ? 'selected' : ''; ?>>Australia</option>
                    <option value="Germany" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'Germany' ? 'selected' : ''; ?>>Germany</option>
                    <option value="France" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'France' ? 'selected' : ''; ?>>France</option>
                    <option value="Japan" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'Japan' ? 'selected' : ''; ?>>Japan</option>
                    <option value="China" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'China' ? 'selected' : ''; ?>>China</option>
                    <option value="Brazil" <?= isset($faq['faqCountry']) && $faq['faqCountry'] === 'Brazil' ? 'selected' : ''; ?>>Brazil</option>
                    <!-- Add more countries as needed -->
                </select>
                <div class="invalid-feedback">
                    Please select a country.
                </div>
            </div>

            <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">
            <input type="hidden" name="postId" id="postId" value="<?= $postId; ?>">

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
<?php
    } // End foreach
} else {
    echo json_encode(["message" => "Post ID not provided."]);
    exit;
}
?>