<?php
    $ip_lock_checked = '';
    $ip_hidden       = 'invisible';

    if ($account->get_ipLock() === 'True') {
        $ip_lock_checked = 'checked';
        $ip_hidden = '';
    }
?>

<div class="d-flex">
    <div class="container w-25">
        <div class="row text-center">
            <div class="col">
                <h3>
                    <?php fix_name_header($_SESSION['name']); ?> Account Settings
                </h3>
            </div>
        </div>

        <div class="row">
            <div class="col text-center">
                <div class="form-check form-switch mb-3 d-flex">
                    <input class="form-check-input align-self-center" type="checkbox" role="switch" id="ip-lock-switch" <?php echo $ip_lock_checked; ?>>
                    <label class="form-check-label" for="ip-lock-switch">IP lock Account</label>
                    <div class="small">
                        <abbr title="An IP lock account can only be accessed by the IP provided in the textbox below" class="initialism align-self-center">[ ? ]</abbr>
                    </div>
                </div>
                <input id="ip-lock-address" name="ip-lock-address" class="mb-3 <?php echo $ip_hidden; ?>" type="text" minlength="7" maxlength="15" size="15" pattern="^[0-9]{1,3}\.(?:[0-9]{1,3}\.){2}[0-9]{1,3}$" value="<?php echo $account->get_ipLockAddr(); ?>" />
            </div>
        </div>
    </div>

    <div class="container w-25">
        <div class="row text-center">
            <div class="col">
                <h3>
                    <?php fix_name_header($_SESSION['name']); ?> Visual Settings
                </h3>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label for="sidebar-type" class="me-3">Sidebar:</label>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <select id="sidebar-type" name="sidebar-type" class="form-select" aria-label="Sidebar Dropdown Menu">
                    <option selected disabled value="-1"></option>
                    <option disabled value="-2">-- Classic --</option>
                    <option value="CLASSIC">Classic side menu</option>
                    <option disabled value="-3">-- AdminLTE Varients --</option>
                    <option value="LTE_DEFAULT">Default side menu</option>
                    <option value="LTE_COLLAPSED">Collapsed side menu</option>
                    <option value="LTE_FIXED">Fixed complete side menu</option>
                    <option value="LTE_MINI">Mini side menu</option>
                    <option value="LTE_UNFIXED">Unfixed side menu</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="text-center">
    <button id="save-settings" name="save-settings" class="btn btn-primary" type="button" onclick="save_settings('ip_lock')">
        Save
    </button>
</div>
<div class="row text-center">
    <div id="status-msg" name="status-msg" class="text-center text-success"></div>
</div>