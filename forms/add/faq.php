<form id="faqForm" novalidate>
    <!-- FAQ Fields -->
    <div class="form-group">
        <label for="faqQuestion">FAQ Question</label>
        <input type="text" class="form-control" id="faqQuestion" name="faqQuestion" placeholder="Enter FAQ question" required />
        <div class="invalid-feedback">
            Please enter a question.
        </div>
    </div>

    <div class="form-group">
        <label for="faqAnswer">FAQ Answer</label>
        <textarea class="form-control" id="faqAnswer" name="faqAnswer" rows="3" placeholder="Enter FAQ answer" required></textarea>
        <div class="invalid-feedback">
            Please enter an answer.
        </div>
    </div>

    <!-- Country Select Box -->
    <div class="form-group">
        <label for="country">Country</label>
        <?php
        $countries = $database->select("countries", ["id", "country_name"]);
        ?>

        <select class="form-control" id="faqCountry" name="faqCountry" required>
            <option value="" selected disabled>Select a country</option>
            <?php foreach ($countries as $country): ?>
                <option value="<?= htmlspecialchars($country['id']); ?>"><?= htmlspecialchars($country['country_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <div class="invalid-feedback">
            Please select a country.
        </div>
    </div>

    <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">

    <button type="submit" class="btn btn-primary">Submit</button>
</form>