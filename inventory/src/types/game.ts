// ============================================
// Legend of Aetheria - TypeScript Type Definitions
// Maps directly to PHP backend data model
// Uses const objects instead of enums for compatibility
// ============================================

/** 11-tier rarity system matching ObjectRarity enum */
export const ObjectRarity = {
  NONE: 'NONE',
  WORTHLESS: 'WORTHLESS',
  TARNISHED: 'TARNISHED',
  COMMON: 'COMMON',
  ENCHANTED: 'ENCHANTED',
  MAGICAL: 'MAGICAL',
  LEGENDARY: 'LEGENDARY',
  EPIC: 'EPIC',
  MYSTIC: 'MYSTIC',
  HEROIC: 'HEROIC',
  INFAMOUS: 'INFAMOUS',
  GODLY: 'GODLY',
} as const;
export type ObjectRarity = (typeof ObjectRarity)[keyof typeof ObjectRarity];

/** Equipment slot types matching ObjectType enum */
export const ObjectType = {
  CONSUMABLES: 'CONSUMABLES',
  HELMET: 'HELMET',
  ARMOR: 'ARMOR',
  WEAPON: 'WEAPON',
  BOOTS: 'BOOTS',
  GLOVES: 'GLOVES',
  LEGGINGS: 'LEGGINGS',
  CHARM: 'CHARM',
  AMULET: 'AMULET',
  RING: 'RING',
  WINGS: 'WINGS',
  QUEST: 'QUEST',
  SHIELD: 'SHIELD',
} as const;
export type ObjectType = (typeof ObjectType)[keyof typeof ObjectType];

/** Character races matching Races enum */
export const CharacterRace = {
  HUMAN: 'HUMAN',
  ELF: 'ELF',
  DWARF: 'DWARF',
  ORC: 'ORC',
  UNDEAD: 'UNDEAD',
  CELESTIAL: 'CELESTIAL',
} as const;
export type CharacterRace = (typeof CharacterRace)[keyof typeof CharacterRace];

/** Character status matching Status enum */
export const CharacterStatus = {
  HEALTHY: 'HEALTHY',
  WOUNDED: 'WOUNDED',
  DEAD: 'DEAD',
} as const;
export type CharacterStatus = (typeof CharacterStatus)[keyof typeof CharacterStatus];

/** Gem color types */
export const GemColor = {
  RUBY: 'RUBY',
  SAPPHIRE: 'SAPPHIRE',
  EMERALD: 'EMERALD',
  AMETHYST: 'AMETHYST',
  TOPAZ: 'TOPAZ',
  DIAMOND: 'DIAMOND',
} as const;
export type GemColor = (typeof GemColor)[keyof typeof GemColor];

/** Item modifier targeting a specific stat */
export interface ItemModifier {
  target: string;       // e.g., "str", "int", "def", "maxHP"
  effects: number[];    // effect values
}

/** A gem socket on an item */
export interface Socket {
  socketID: number;
  itemID: number | null;
  gem: Gem | null;
  modifiers: ItemModifier | null;
}

/** A gem that can be socketed into items */
export interface Gem {
  gemID: number;
  name: string;
  color: GemColor;
  tier: number;         // 1-10 tier system
  modifiers: ItemModifier[];
  image: string;
}

/** An inventory item */
export interface Item {
  id: number;
  name: string;
  image: string;
  imgThumb: string;
  weight: number;
  itemId: number;       // schema item ID
  type: ObjectType;     // equipment category
  subtype: string;      // e.g., "sword", "plate", "leather"
  maxSockets: number;
  rarity: ObjectRarity;
  expireTick: number | null;
  implicit: ItemModifier[];
  affixPool: ItemModifier[];
  description: string;
  sockets: Socket[];
  modifiers: ItemModifier[];
  stackable: boolean;
  quantity?: number;    // for stackable items
}

/** Character stats matching Stats.php */
export interface CharacterStats {
  hp: number;
  mp: number;
  maxHP: number;
  maxMP: number;
  str: number;
  dex: number;
  int: number;
  spd: number;
  def: number;
  mdef: number;
  crit: number;
  acc: number;
  dodge: number;
  block: number;
  resist: number;
}

/** Inventory system matching Inventory.php */
export interface Inventory {
  id: number;           // character ID
  slotCount: number;    // default 20
  currentWeight: number;
  maxWeight: number;    // default 1000
  nextAvailableSlot: number;
  slots: (Item | null)[];
}

/** Character data matching Character.php */
export interface Character {
  id: number;
  accountID: number;
  level: number;
  x: number;
  y: number;
  alignment: number;
  spindels: number;
  exp: number;
  dateCreated: string;
  floor: number;
  gold: number;
  description: string;
  location: string;
  name: string;
  avatar: string;
  lastAction: string;
  status: CharacterStatus;
  race: CharacterRace | null;
  stats: CharacterStats;
  inventory: Inventory;
}

/** Equipment slots configuration for paper doll */
export interface EquipmentSlot {
  type: ObjectType;
  label: string;
  position: { x: number; y: number }; // percentage positions
  size: 'normal' | 'small' | 'wide';
}

/** Drag-and-drop context data */
export interface DragData {
  item: Item;
  sourceType: 'inventory' | 'equipment';
  sourceIndex: number;
}

/** Crafting recipe */
export interface CraftRecipe {
  id: number;
  name: string;
  description: string;
  ingredients: { item: Item; quantity: number }[];
  result: Item;
  goldCost: number;
}

/** Upgrade recipe for items */
export interface UpgradeRecipe {
  id: number;
  name: string;
  description: string;
  baseItem: ObjectType;
  materials: { item: Item; quantity: number }[];
  goldCost: number;
  spindelCost: number;
}
