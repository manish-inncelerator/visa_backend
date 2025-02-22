<form id="requiredDocumentForm" novalidate>
    <div class="form-group">
        <label for="requiredDocumentName">Required Document</label>
        <input type="text" class="form-control" id="requiredDocumentName" name="requiredDocumentName" placeholder="Enter required document name" required />
        <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">
        <div class="invalid-feedback">
            Please enter a name for required document.
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>