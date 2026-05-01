<?php
namespace Game\Inventory\Enums;
use Game\Traits\EnumExtender\EnumExtender;

enum ObjectType {
	use EnumExtender;
	
	public function icon(): string {
		return match($this) {
			ObjectType::CONSUMABLES => '<span class="text-success"><i class="ra ra-flask ra-lg p-2"></i></span>',
			ObjectType::HELMET     => '<span class="text-secondary"><i class="ra ra-knight-helmet ra-lg p-2"></i></span>',
			ObjectType::ARMOR      => '<span class="text-secondary"><i class="ra ra-vest ra-lg p-2"></i></span>',
			ObjectType::WEAPON     => '<span class="text-danger"><i class="ra ra-crossed-swords ra-lg p-2"></i></span>',
			ObjectType::BOOTS      => '<span class="text-warning"><i class="ra ra-boot-stomp ra-lg p-2"></i></span>',
			ObjectType::GLOVES     => '<span class="text-info"><i class="ra ra-hand ra-lg p-2"></i></span>',
			ObjectType::LEGGINGS   => '<span class="text-secondary"><i class="ra ra-player ra-lg p-2"></i></span>',
			ObjectType::CHARM      => '<span class="text-info"><i class="ra ra-triforce ra-lg p-2"></i></span>',
			ObjectType::AMULET     => '<span class="text-warning"><i class="ra ra-gem-pendant ra-lg p-2"></i></span>',
			ObjectType::RING       => '<span class="text-light"><i class="ra ra-circle-of-circles ra-lg p-2"></i></span>',
			ObjectType::WINGS      => '<span class="text-primary"><i class="ra ra-feathered-wing ra-lg p-2"></i></span>',
			ObjectType::QUEST      => '<span class="text-warning quest-icon"><i class="ra ra-scroll-unfurled ra-lg p-2"></i></span>',
		};
	}

	case CONSUMABLES;
	case HELMET;
	case ARMOR;
	case WEAPON;
	case BOOTS;
	case GLOVES;
	case LEGGINGS;
	case CHARM;
	case AMULET;
	case RING;
	case WINGS;
}
