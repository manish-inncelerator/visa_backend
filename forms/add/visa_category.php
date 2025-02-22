<form id="visaCategoryForm" novalidate>
    <div class="form-group">
        <label for="visaCategory">Visa Category</label>
        <input type="text" class="form-control" id="visaCategory" name="visaCategory" placeholder="Enter Visa Category" required />
        <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">
        <div class="invalid-feedback">
            Please enter which type of visa it is?
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>