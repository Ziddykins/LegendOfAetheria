<?php
    $character->bank->add_goldAmount(100);
    $character->bank->set_accountID(1);
?>
<div class="container">
    <div class="card shadow-sm">
        <div class="card-body bg-dark bg-opacity-25 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="card-title h4 mb-0"><?php echo fix_name_header($character->get_name()); ?>'s Bank Account</h2>
            </div>
            
            <div class="row g-4 mb-4">
                <!-- Account Summary -->
                <div class="col-md-6">
                    <div class="card h-100 bg-dark bg-opacity-10">
                        <div class="card-header">
                            <h3 class="h5 mb-0">Account Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush bg-transparent">
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-secondary">
                                    <span>Banked Gold</span>
                                    <span class="badge bg-primary rounded-pill"><?php echo $character->bank->get_goldAmount(); ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-secondary">
                                    <span>Gold On-Hand</span>
                                    <span class="badge bg-secondary rounded-pill"><?php echo $character->get_gold(); ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-secondary">
                                    <span>Active Loans</span>
                                    <span class="badge bg-danger rounded-pill"><?php echo $character->bank->get_loan(); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interest Info -->
                <div class="col-md-6">
                    <div class="card h-100 bg-dark bg-opacity-10">
                        <div class="card-header">
                            <h3 class="h5 mb-0">Interest Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush bg-transparent">
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-secondary">
                                    <span>Interest Rate</span>
                                    <span class="badge bg-primary rounded-pill"><?php echo $character->bank->get_interestRate(); ?>%</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-secondary">
                                    <span>Net Daily Earnings</span>
                                    <span class="badge bg-success rounded-pill"><?php echo $character->bank->get_interestRate() * $character->bank->get_goldAmount(); ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-secondary">
                                    <span>Daily Percentage Rate</span>
                                    <span class="badge bg-warning text-black rounded-pill"><?php echo $character->bank->get_dpr(); ?>.00%</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-secondary">
                                    <span>Gross Rate</span>
                                    <span class="badge bg-danger rounded-pill"><?php echo $character->bank->get_interestRate(); ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Section -->
            <h3 class="h5 mb-3">Manage Transactions</h3>
            <div class="row g-4">
                <!-- Gold Transactions -->
                <div class="col-md-6">
                    <div class="card bg-dark bg-opacity-10">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="h6 mb-0">Gold Management</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="gold-amount" class="form-label">Amount to Transfer</label>
                                <div class="input-group">
                                    <input id="gold-amount" class="form-control" type="number" min="1" max="<?php echo $character->get_gold(); ?>" placeholder="Enter amount">
                                    <span class="input-group-text">Gold</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary flex-grow-1">
                                    <i class="bi bi-arrow-90deg-right me-1"></i>Withdraw
                                </button>
                                <button class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-arrow-90deg-left me-1"></i>Deposit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Spindels Transactions -->
                <div class="col-md-6">
                    <div class="card bg-dark bg-opacity-10">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="h6 mb-0">Spindels Management</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="spindels-amount" class="form-label">Amount to Transfer</label>
                                <div class="input-group">
                                    <input id="spindels-amount" class="form-control" type="number" placeholder="Enter amount">
                                    <span class="input-group-text">Spindels</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary flex-grow-1">
                                    <i class="bi bi-arrow-90deg-right me-1"></i>Withdraw
                                </button>
                                <button class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-arrow-90deg-left me-1"></i>Deposit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

