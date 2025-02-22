<?php
require_once 'Database.php'; // Ensure the Database.php file is correctly included

// Initialize variables to avoid undefined variable warnings
$visaTypes = [];
$visaKinds = [];
$visaCategories = [];

try {
    // Query for visa types
    $visaTypes = $database->select('visa_types', ['visa_type', 'id'], [
        "ORDER" => ["id" => "DESC"] // Order by ID in descending order
    ]);
} catch (Exception $e) {
    // Handle any errors
    echo "Error fetching visa types: " . $e->getMessage();
}

try {
    // Query for visa kinds
    $visaKinds = $database->select('visa_kinds', ['visa_kind', 'id'], [
        "ORDER" => ["id" => "DESC"] // Order by ID in descending order
    ]);
} catch (Exception $e) {
    // Handle any errors
    echo "Error fetching visa kinds: " . $e->getMessage();
}

try {
    // Query for visa categories
    $visaCategories = $database->select('visa_categories', ['visa_category', 'id'], [
        "ORDER" => ["id" => "DESC"] // Order by ID in descending order
    ]);
} catch (Exception $e) {
    // Handle any errors
    echo "Error fetching visa categories: " . $e->getMessage();
}

try {
    // Query for required documents
    $requiredDocuments = $database->select('required_documents', ['required_document_name', 'id'], [
        "ORDER" => ["id" => "DESC"] // Order by ID in descending order
    ]);
} catch (Exception $e) {
    // Handle any errors
    echo "Error fetching required documents: " . $e->getMessage();
}


?>
<!-- Card Container -->
<div class="card">
    <div class="card-header h5 font-weight-bold">
        Add a country
    </div>
    <div class="card-body">
        <form id="countryForm" novalidate>
            <div class="row">
                <!-- Country Name -->
                <div class=" col-12 form-group">
                    <label for="countryName">
                        Country Name
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="countryNameInfo">
                                Enter the official name of the country.
                            </div>
                        </span>
                    </label>
                    <input type="text" class="form-control" id="countryName" name="countryName" placeholder="Enter country name" required>
                    <div class="invalid-feedback">
                        Please enter the official name of the country.
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Easy Serviceability -->
                <div class="col-12 form-group">
                    <label for="serviceability">
                        Country Easy Serviceable
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="serviceabilityInfo">
                                Select whether the country is easy to service (e.g., visa processing is straightforward).
                            </div>
                        </span>
                    </label>
                    <select class="form-control" id="serviceability" name="serviceability" required>

                        <option value="" disabled="disabled" selected="selected">Select serviceability</option>
                        <option value="easy">Easy</option>
                        <option value="not_easy">Not Easy</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select whether the country is easily serviceable.
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Pricing Section -->
                <div class="col-12 form-group d-none" id="pricingSection">
                    <p><b>Pricing</b></p>

                    <!-- Portify Fees -->
                    <label for="portifyFees">
                        Visa Assitance Fee
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="portifyFeesInfo">
                                Enter the visa assistance fees charged by Portify.
                            </div>
                        </span>
                    </label>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control" id="portifyFees" name="portifyFees" placeholder="Enter Visa Assistance Fee">
                        <div class="input-group-append">
                            <span class="input-group-text">SGD</span>
                        </div>
                        <div class="invalid-feedback">
                            Please enter the Visa Assistance fees.
                        </div>
                    </div>

                    <!-- VFS Service Fee -->
                    <label for="VFSService">
                        VFS Service Fee
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="VFSServiceInfo">
                                Enter the VFS service fee for visa processing.
                            </div>
                        </span>
                    </label>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control" id="VFSService" name="VFSService" placeholder="Enter VFS Service Fee">
                        <div class="input-group-append">
                            <span class="input-group-text">SGD</span>
                        </div>
                        <div class="invalid-feedback">
                            Please enter the VFS Service Fee.
                        </div>
                    </div>

                    <!-- Only Embassy Fees -->
                    <div class="embassyFeesDiv">
                        <div id="onlyEmbassyFeeSection">
                            <label for="onlyEmbassyFee">
                                Embassy Fee
                                <span class="dropdown">
                                    <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                                    <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="onlyEmbassyFeeInfo">
                                        Enter the flat embassy fee for visa processing.
                                    </div>
                                </span>
                            </label>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" id="onlyEmbassyFee" name="onlyEmbassyFee" placeholder="Enter Embassy Fee">
                                <div class="input-group-append">
                                    <span class="input-group-text">SGD</span>
                                </div>
                                <div class="invalid-feedback">
                                    Please enter the only embassy fee.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visa Types Dropdown -->
            <div class="row">
                <div class="col-lg-3 col-12 form-group">
                    <label for="visaType">
                        Visa Type
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="visaTypeInfo">
                                Select the type of visa (e.g., Sticker, eVisa, etc.).
                            </div>
                        </span>
                    </label>
                    <select class="form-control" id="visaType" name="visaType" required>
                        <option value="" selected disabled>Please Select</option>
                        <?php if (!empty($visaTypes)): ?>
                            <?php foreach ($visaTypes as $visa): ?>
                                <option value="<?= $visa['id'] ?>"><?= htmlspecialchars($visa['visa_type']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No visa types available</option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">
                        Please select a visa type.
                    </div>
                </div>

                <!-- Visa Kinds Dropdown -->
                <div class="col-lg-3 col-12 form-group">
                    <label for="visaKind">
                        Visa Kind
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="visaKindInfo">
                                Select the kind of visa (e.g., Tourist Visa, Business Visa, etc.).
                            </div>
                        </span>
                    </label>
                    <select class="form-control" id="visaKind" name="visaKind" required>
                        <option value="" selected disabled>Please Select</option>
                        <?php if (!empty($visaKinds)): ?>
                            <?php foreach ($visaKinds as $kind): ?>
                                <option value="<?= $kind['id'] ?>"><?= htmlspecialchars($kind['visa_kind']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No visa kinds available</option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">
                        Please select a visa kind.
                    </div>
                </div>

                <!-- Visa Categories Dropdown -->
                <div class="col-lg-3 col-12 form-group">
                    <label for="visaCategory">
                        Visa Category
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="visaCategoryInfo">
                                Select the category of visa (e.g., Visa in a Week, Instant Visa, etc.).
                            </div>
                        </span>
                    </label>
                    <select class="form-control" id="visaCategory" name="visaCategory" required>
                        <option value="" selected disabled>Please Select</option>
                        <?php if (!empty($visaCategories)): ?>
                            <?php foreach ($visaCategories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['visa_category']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No visa categories available</option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">
                        Please select a visa category.
                    </div>
                </div>

                <!-- Visa Entry -->
                <div class="col-lg-3 col-12 form-group">
                    <label for="visaEntry">
                        Visa Entry
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="visaEntryInfo">
                                Select the type of visa entry (e.g., Single Entry, Multiple Entry, etc.).
                            </div>
                        </span>
                    </label>
                    <select class="form-control" id="visaEntry" name="visaEntry" required>
                        <option value="" selected disabled>Please Select</option>
                        <option value="single">Single Entry</option>
                        <option value="double">Double Entry</option>
                        <option value="multiple">Multiple Entry</option>
                        <option value="transit">Transit Entry</option>
                        <option value="limited">Limited Entry (3 Entries)</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select type of visa entry.
                    </div>
                </div>
            </div>

            <!-- Stay Duration -->
            <div class="row">
                <div class="col-lg-6 col-12 form-group">
                    <label for="stayDuration">
                        Stay Duration
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="stayDurationInfo">
                                Enter the maximum allowed stay duration in days.
                            </div>
                        </span>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="stayDuration" name="stayDuration" placeholder="Enter stay duration" required>
                        <div class="input-group-append">
                            <span class="input-group-text">days</span>
                        </div>
                        <div class="invalid-feedback">
                            Please enter the stay duration.
                        </div>
                    </div>
                </div>

                <!-- Visa Validity -->
                <div class="col-lg-6 col-12 form-group">
                    <label for="visaValidity">
                        Visa Validity
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="visaValidityInfo">
                                Enter the visa validity period and select the unit (days, weeks, or months).
                            </div>
                        </span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Up to</span>
                        </div>
                        <input type="number" class="form-control" id="visaValidity" name="visaValidity" placeholder="Enter visa validity" required min="1">
                        <div class="input-group-append">
                            <select class="form-control" id="validityUnit" name="validityUnit" required>
                                <option value="days">days</option>
                                <option value="weeks">weeks</option>
                                <option value="months">months</option>
                            </select>
                        </div>
                        <div class="invalid-feedback">
                            Please enter the visa validity.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processing Time -->
            <div class="row">
                <div class="col-lg-6 col-12 form-group">
                    <label for="processingTime">
                        Visa Processing Time
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="processingTimeInfo">
                                Enter the visa processing time and select the unit (hours, days, weeks, or months).
                            </div>
                        </span>
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="processingTimeValue" name="processingTimeValue" placeholder="Enter a number" required>
                        <select class="form-control" id="processingTimeUnit" name="processingTimeUnit" required>
                            <option value="" selected disabled>Please Select</option>
                            <option value="hours">Hours</option>
                            <option value="weeks">Weeks</option>
                            <option value="days">Days</option>
                            <option value="months">Months</option>
                        </select>
                    </div>
                    <div class="invalid-feedback">
                        Please enter the processing time and select a unit.
                    </div>
                </div>

                <!-- Approval Rate -->
                <div class="col-lg-6 col-12 form-group">
                    <label for="approvalRate">
                        Visa Approval Rate
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="approvalRateInfo">
                                Enter the visa approval rate as a percentage.
                            </div>
                        </span>
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="approvalRate" name="approvalRate" placeholder="Enter approval rate" required>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="invalid-feedback">
                            Please enter the approval rate.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Required Documents -->
            <div class="row">
                <div class="col-lg-6 col-12 form-group">
                    <label for="requiredDocuments">
                        Required Documents
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="requiredDocumentsInfo">
                                Select the required documents for the visa application.
                            </div>
                        </span>
                    </label>
                    <select multiple class="form-control" id="requiredDocuments" name="requiredDocuments[]" required>
                        <?php if (!empty($requiredDocuments)): ?>
                            <?php foreach ($requiredDocuments  as $document): ?>
                                <option value="<?= $document['id'] ?>"><?= htmlspecialchars($document['required_document_name']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No visa kinds available</option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">
                        Please select at least one required document.
                    </div>
                </div>

                <!-- Visa Department Dropdown -->
                <div class="col-lg-6 col-12 form-group">
                    <label for="visaDepartment">
                        Visa Department
                        <span class="dropdown">
                            <i class="bi bi-info-circle text-primary" data-toggle="dropdown dropdown-helper" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="visaDepartmentInfo">
                                Select the visa department (Inbound, Outbound, or Both).
                            </div>
                        </span>
                    </label>
                    <select class="form-control" id="visaDepartment" name="visaDepartment" required>
                        <option value="" selected disabled>Please Select</option>
                        <option value="inbound">Inbound</option>
                        <option value="outbound">Outbound</option>
                        <option value="both">Both</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select a visa department.
                    </div>
                </div>
            </div>

            <!-- Hidden Field for Pricing Visibility -->
            <input type="hidden" id="pricingVisible" name="pricingVisible" value="1">

            <!-- Hidden Field for EmbassyFeesType Visibility -->
            <input type="hidden" id="embassyFeesTypeVisible" name="embassyFeesTypeVisible" value="1">

            <!-- Hidden Field for Admin ID -->
            <input type="hidden" id="adminID" name="adminID" value="<?= $_SESSION['admin_id']; ?>">

            <!-- Submit Button -->
            <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> Submit</button>
            <span class="float-right"><b>Total Pricing: SGD <span id="totalPricing"></span></b></span>
        </form>
    </div>
</div>