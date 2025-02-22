<div class="card">
    <div class="card-header h5 font-weight-bold">
        Create a page
    </div>
    <div class="card-body">
        <form id="pageForm" novalidate>
            <div class="row">
                <!-- Page Name -->
                <div class="col-12 form-group">
                    <label for="pageName">Page Name</label>
                    <input type="text" class="form-control" id="pageName" name="pageName" placeholder="Enter page name" required>
                    <div class="invalid-feedback">
                        Please enter a page name
                    </div>
                </div>

                <!-- Page Slug -->
                <div class="col-12 form-group">
                    <label for="pageDetails">Page's Slug</label>
                    <!-- <textarea class="form-control" id="pageSlug" name="pageSlug" rows="4" placeholder="Enter page details"></textarea> -->
                    <input type="text" class="form-control" name="pageSlug" id="pageSlug">
                </div>

                <!-- Page Description -->
                <div class="col-12 form-group">
                    <label for="pageDescription">Page Description</label>
                    <textarea class="form-control" id="pageDescription" name="pageDescription" placeholder="Enter page description" required></textarea>
                    <div class="invalid-feedback">
                        Please enter a page description
                    </div>
                </div>


                <!-- Page Details -->
                <div class="col-12 form-group">
                    <label for="pageDetails">Page Content</label>
                    <textarea class="form-control" id="pageDetails" name="pageDetails" rows="4" placeholder="Enter page details"></textarea>
                </div>

                <!-- Page Position -->
                <div class="col-12 form-group">
                    <label for="pagePosition">Page Position</label>
                    <select class="form-control" id="pagePosition" name="pagePosition" required>
                        <option value="" disabled selected>Select position</option>
                        <option value="header">Header</option>
                        <option value="footer">Footer</option>
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
                    <button type="submit" class="btn btn-primary">Create Page</button>
                </div>
            </div>
        </form>
    </div>
</div>