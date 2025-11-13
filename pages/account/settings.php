<?php
    extract([
        'ip_lock_checked' => $account->get_ipLock() === true ? 'checked' : '',
        'ip_hidden' => $account->get_ipLock() === true ? '' : 'invisible'
    ]);
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center g-4 mx-auto" style="max-width: 1200px">
        <!-- Account Settings Card -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title h5 mb-0">Account Settings</h3>
                    <div class="hr"></div>
                </div>
                <div class="card-body">
                    <form id="ip-lock-form" class="needs-validation" novalidate>
                        <div class="form-group">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="ip-lock-switch" <?php echo $ip_lock_checked; ?>>
                                    <label class="form-check-label" for="ip-lock-switch">IP Lock</label>
                                </div>
                                <span class="badge" data-bs-toggle="tooltip" title="IP lock restricts account access to specified IP">
                                    <span class="material-symbols-outlined">shield</span>
                                </span>
                            </div>
                            <div class="ip-lock-input-group">
                                <input id="ip-lock-address" name="ip-lock-address" 
                                       class="form-control form-control-sm <?php echo $ip_hidden; ?>"
                                       type="text" 
                                       pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                       placeholder="Enter IP address"
                                       value="<?php echo $account->get_ipLockAddr(); ?>" 
                                       required>
                                <div class="invalid-feedback">Please enter a valid IP address</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Visual Settings Card -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title h5 mb-0">Visual Settings</h3>
                </div>
                <div class="card-body">
                    <form id="visual-settings-form" class="needs-validation" novalidate>
                        <div class="form-group mb-3">
                            <label for="sidebar-type" class="form-label">Sidebar Style</label>
                            <select id="sidebar-type" name="sidebar-type" class="form-select" aria-label="Sidebar Dropdown Menu">
                                <option selected disabled value="-1">Select sidebar style</option>
                                <optgroup label="Classic">
                                    <option value="CLASSIC">Classic side menu</option>
                                </optgroup>
                                <optgroup label="AdminLTE Variants">
                                    <option value="LTE_DEFAULT">Default side menu</option>
                                    <option value="LTE_COLLAPSED">Collapsed side menu</option>
                                    <option value="LTE_FIXED">Fixed complete side menu</option>
                                    <option value="LTE_MINI">Mini side menu</option>
                                    <option value="LTE_UNFIXED">Unfixed side menu</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="theme-type" class="form-label">Theme</label>
                            <select id="theme-type" name="theme-type" class="form-select" aria-label="Theme Dropdown Menu">
                                <option selected disabled value="-1">Select theme</option>
                                <option value="LIGHT">Light theme</option>
                                <option value="DARK">Dark theme</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="font-type" class="form-label">Font</label>
                            <select id="font-type" name="font-type" class="form-select" aria-label="Font Dropdown Menu">
                                <option selected disabled value="-1">Select font</option>
                                <optgroup label="Sans Serif">
                                    <option value="Roboto" style="font-family: 'Roboto'">Roboto</option>
                                    <option value="Open-Sans" style="font-family: 'Open Sans'">Open Sans</option>
                                    <option value="Lato" style="font-family: 'Lato'">Lato</option>
                                </optgroup>
                                <optgroup label="Serif">
                                    <option value="Merriweather" style="font-family: 'Merriweather'">Merriweather</option>
                                    <option value="PT-Serif" style="font-family: 'PT Serif'">PT Serif</option>
                                </optgroup>
                                <optgroup label="Display">
                                    <option value="Montserrat" style="font-family: 'Montserrat'">Montserrat</option>
                                    <option value="Poppins" style="font-family: 'Poppins'">Poppins</option>
                                </optgroup>
                            </select>
                            <div class="mt-2 p-2 border rounded" id="font-preview" style="min-height: 50px">
                                The quick brown fox jumps over the lazy dog
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 text-center">
            <button id="save-settings" type="submit" class="btn btn-primary px-4 position-relative">
                <span class="material-symbols-outlined me-2">save</span>Save
                <span class="spinner-border spinner-border-sm d-none position-absolute" 
                      role="status" aria-hidden="true"></span>
            </button>
            <div id="status-msg" class="mt-3 text-success"></div>
        </div>
    </div>
</div>

<script src="/js/settings.js"></script>