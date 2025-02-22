<form id="visaKindForm" novalidate>
    <div class="form-group">
        <label for="VisaKind">Visa Kind</label>
        <input type="text" class="form-control" id="visaKind" name="visaKind" placeholder="Enter Visa Kind" required />
        <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">
        <div class="invalid-feedback">
            Please enter which kind of visa it is?
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>