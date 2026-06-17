<?php
	include 'snippets/snip-charstats.php';
	use Game\Inventory\Inventory;
	use Game\Inventory\Items\Item;

	global $character;

	$inventory = new Inventory($character->get_id());
	

	$test_item = new Item('consumable', 1);

	$inventory->addItem($test_item);
	echo $slots_left = $inventory->spacesLeft();
	echo '<pre>';
	print_r($inventory->dumpProps());
	echo '</pre>';
	exit();
?>
	<div class="inventory-page py-5">
		<div class="container-lg">
			<div class="row g-4">
				<!-- Left Panel: Character Inventory Grid -->
				<div class="col-lg-8">
					<div class="inventory-panel">
						<div class="inventory-header">
							<h2 class="inventory-title">Backpack</h2>
							<span class="inventory-weight">23/50 slots</span>
						</div>
						<div class="inventory-grid">
							<?php for ($i = 0; $i < 50; $i++): ?>
								<div class="inventory-slot" data-slot="<?php echo $i; ?>">
									<?php if ($i === 0): ?>
										<div class="inventory-item" data-rarity="rare">
											<img src="/img/items/sword-icon.png" alt="Item" class="inventory-item-icon" />
											<div class="inventory-item-quantity">1</div>
										</div>
									<?php endif; ?>
								</div>
							<?php endfor; ?>
						</div>
					</div>
				</div>

				<!-- Right Panel: Character Stats & Equipment -->
				<div class="col-lg-4">
					<div class="character-panel mb-4">
						<div class="character-portrait text-center">
							<img src="/img/avatars/avatar-unknown.webp" alt="Character" class="img-fluid rounded" />
						</div>
						<div class="character-info mt-3">
							<h3 class="text-white mb-2">Warrior</h3>
							<div class="stats-grid">
								<div class="stat-item">
									<span class="stat-label">Level</span>
									<span class="stat-value">12</span>
								</div>
								<div class="stat-item">
									<span class="stat-label">Exp</span>
									<span class="stat-value">2,450</span>
								</div>
								<div class="stat-item">
									<span class="stat-label">Gold</span>
									<span class="stat-value text-warning">15.2K</span>
								</div>
							</div>
						</div>
					</div>

					<!-- Equipment Slots -->
					<div class="equipment-panel">
						<h4 class="equipment-title">Equipment</h4>
						<div class="equipment-slots">
							<div class="equipment-slot head">
								<span class="slot-label">Head</span>
								<div class="slot-display">
									<i class="bi bi-shield-check text-warning opacity-25"></i>
								</div>
							</div>
							<div class="equipment-slot chest">
								<span class="slot-label">Chest</span>
								<div class="slot-display">
									<i class="bi bi-shield-check text-warning opacity-25"></i>
								</div>
							</div>
							<div class="equipment-slot hands">
								<span class="slot-label">Hands</span>
								<div class="slot-display">
									<i class="bi bi-shield-check text-warning opacity-25"></i>
								</div>
							</div>
							<div class="equipment-slot feet">
								<span class="slot-label">Feet</span>
								<div class="slot-display">
									<i class="bi bi-shield-check text-warning opacity-25"></i>
								</div>
							</div>
							<div class="equipment-slot main-hand">
								<span class="slot-label">Main Hand</span>
								<div class="slot-display">
									<i class="bi bi-shield-check text-warning opacity-25"></i>
								</div>
							</div>
							<div class="equipment-slot off-hand">
								<span class="slot-label">Off Hand</span>
								<div class="slot-display">
									<i class="bi bi-shield-check text-warning opacity-25"></i>
								</div>
							</div>
						</div>
					</div>

					<!-- Item Details -->
					<div class="item-details-panel mt-4">
						<h4 class="text-white mb-3">Item Details</h4>
						<div class="alert alert-info-subtle border-subtle">
							<p class="text-muted small mb-0">Select an item to view details</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
