<form id="visaTypeForm" novalidate>
    <div class="form-group">
        <label for="visaType">Visa Type</label>
        <input type="text" class="form-control" id="visaType" name="visaType" placeholder="Enter Visa Type" required />
        <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">
        <div class="invalid-feedback">
            Please enter which type of visa it is?
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>