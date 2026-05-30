// ============================================
// Inventory State Management Hook
// Manages drag-and-drop, equipping, socketing
// ============================================
import { useState, useCallback } from 'react';
import type { Item, Inventory, CharacterStats, Gem } from '@/types/game';
import { ObjectRarity, ObjectType, GemColor } from '@/types/game';

/** Default empty item */
export const createEmptyItem = (): Item => ({
  id: 0,
  name: '',
  image: '',
  imgThumb: '',
  weight: 0,
  itemId: 0,
  type: ObjectType.CONSUMABLES,
  subtype: '',
  maxSockets: 0,
  rarity: ObjectRarity.NONE,
  expireTick: null,
  implicit: [],
  affixPool: [],
  description: '',
  sockets: [],
  modifiers: [],
  stackable: false,
});

/** Check if item is empty */
export const isEmptyItem = (item: Item | null): boolean => {
  return !item || item.id === 0 || item.name === '';
};

/** Get rarity color for UI theming */
export const getRarityColor = (rarity: ObjectRarity): string => {
  const colors: Record<ObjectRarity, string> = {
    [ObjectRarity.NONE]: '#6b7280',
    [ObjectRarity.WORTHLESS]: '#6b7280',
    [ObjectRarity.TARNISHED]: '#78716c',
    [ObjectRarity.COMMON]: '#f5f5f4',
    [ObjectRarity.ENCHANTED]: '#22c55e',
    [ObjectRarity.MAGICAL]: '#3b82f6',
    [ObjectRarity.LEGENDARY]: '#f59e0b',
    [ObjectRarity.EPIC]: '#a855f7',
    [ObjectRarity.MYSTIC]: '#ec4899',
    [ObjectRarity.HEROIC]: '#ef4444',
    [ObjectRarity.INFAMOUS]: '#dc2626',
    [ObjectRarity.GODLY]: '#fbbf24',
  };
  return colors[rarity] || colors[ObjectRarity.COMMON];
};

/** Get rarity glow effect */
export const getRarityGlow = (rarity: ObjectRarity): string => {
  const glows: Record<ObjectRarity, string> = {
    [ObjectRarity.NONE]: 'none',
    [ObjectRarity.WORTHLESS]: 'none',
    [ObjectRarity.TARNISHED]: 'none',
    [ObjectRarity.COMMON]: 'none',
    [ObjectRarity.ENCHANTED]: '0 0 8px rgba(34, 197, 94, 0.4)',
    [ObjectRarity.MAGICAL]: '0 0 10px rgba(59, 130, 246, 0.5)',
    [ObjectRarity.LEGENDARY]: '0 0 12px rgba(245, 158, 11, 0.6)',
    [ObjectRarity.EPIC]: '0 0 14px rgba(168, 85, 247, 0.6)',
    [ObjectRarity.MYSTIC]: '0 0 16px rgba(236, 72, 153, 0.7)',
    [ObjectRarity.HEROIC]: '0 0 18px rgba(239, 68, 68, 0.7)',
    [ObjectRarity.INFAMOUS]: '0 0 20px rgba(220, 38, 38, 0.8)',
    [ObjectRarity.GODLY]: '0 0 24px rgba(251, 191, 36, 0.9)',
  };
  return glows[rarity] || 'none';
};

/** Get gem color hex */
export const getGemColorHex = (color: GemColor): string => {
  const colors: Record<GemColor, string> = {
    [GemColor.RUBY]: '#ef4444',
    [GemColor.SAPPHIRE]: '#3b82f6',
    [GemColor.EMERALD]: '#22c55e',
    [GemColor.AMETHYST]: '#a855f7',
    [GemColor.TOPAZ]: '#f59e0b',
    [GemColor.DIAMOND]: '#e5e7eb',
  };
  return colors[color] || '#6b7280';
};

/** Get gem image path */
export const getGemImage = (color: GemColor): string => {
  const images: Record<GemColor, string> = {
    [GemColor.RUBY]: '/gem-ruby.png',
    [GemColor.SAPPHIRE]: '/gem-sapphire.png',
    [GemColor.EMERALD]: '/gem-emerald.png',
    [GemColor.AMETHYST]: '/gem-amethyst.png',
    [GemColor.TOPAZ]: '/gem-topaz.png',
    [GemColor.DIAMOND]: '/gem-diamond.png',
  };
  return images[color] || '/gem-ruby.png';
};

/** Equipment slot type mapping (which item types go in which slots) */
export const EQUIPMENT_SLOT_TYPES: ObjectType[] = [
  ObjectType.HELMET,
  ObjectType.AMULET,
  ObjectType.ARMOR,
  ObjectType.WEAPON,
  ObjectType.SHIELD,
  ObjectType.GLOVES,
  ObjectType.RING,
  ObjectType.RING,
  ObjectType.LEGGINGS,
  ObjectType.BOOTS,
  ObjectType.CHARM,
  ObjectType.WINGS,
];

/** Check if item can be equipped in slot */
export const canEquipInSlot = (item: Item, slotType: ObjectType): boolean => {
  if (item.type === ObjectType.WEAPON && slotType === ObjectType.WEAPON) return true;
  if (item.type === ObjectType.SHIELD && slotType === ObjectType.SHIELD) return true;
  return item.type === slotType;
};

/** Calculate total stats from equipment */
export const calculateEquippedStats = (equipment: (Item | null)[]): Partial<CharacterStats> => {
  const totals: Partial<CharacterStats> = {};
  equipment.forEach(item => {
    if (!item || isEmptyItem(item)) return;
    item.modifiers.forEach(mod => {
      const current = (totals as Record<string, number>)[mod.target] || 0;
      (totals as Record<string, number>)[mod.target] = current + mod.effects.reduce((a, b) => a + b, 0);
    });
    item.sockets.forEach(socket => {
      if (socket.gem) {
        socket.gem.modifiers.forEach(mod => {
          const current = (totals as Record<string, number>)[mod.target] || 0;
          (totals as Record<string, number>)[mod.target] = current + mod.effects.reduce((a, b) => a + b, 0);
        });
      }
    });
  });
  return totals;
};

/** Demo items for showcasing the UI */
export const createDemoItems = (): Item[] => [
  {
    id: 1, name: 'Shadowbane', image: '', imgThumb: '', weight: 15, itemId: 101,
    type: ObjectType.WEAPON, subtype: 'sword', maxSockets: 2,
    rarity: ObjectRarity.LEGENDARY, expireTick: null,
    implicit: [{ target: 'str', effects: [5] }],
    affixPool: [{ target: 'crit', effects: [8] }],
    description: 'A blade forged in the abyss.',
    sockets: [{ socketID: 0, itemID: 1, gem: null, modifiers: null }],
    modifiers: [{ target: 'str', effects: [12] }, { target: 'crit', effects: [5] }],
    stackable: false,
  },
  {
    id: 2, name: 'Aegis of Dawn', image: '', imgThumb: '', weight: 25, itemId: 102,
    type: ObjectType.ARMOR, subtype: 'plate', maxSockets: 3,
    rarity: ObjectRarity.EPIC, expireTick: null,
    implicit: [{ target: 'def', effects: [15] }],
    affixPool: [{ target: 'maxHP', effects: [50] }],
    description: 'Radiant armor of the morning sun.',
    sockets: [
      { socketID: 0, itemID: 2, gem: null, modifiers: null },
      { socketID: 1, itemID: 2, gem: null, modifiers: null },
    ],
    modifiers: [{ target: 'def', effects: [25] }, { target: 'maxHP', effects: [80] }],
    stackable: false,
  },
  {
    id: 3, name: 'Helm of Whispers', image: '', imgThumb: '', weight: 10, itemId: 103,
    type: ObjectType.HELMET, subtype: 'helmet', maxSockets: 1,
    rarity: ObjectRarity.MAGICAL, expireTick: null,
    implicit: [{ target: 'int', effects: [3] }],
    affixPool: [],
    description: 'Hear the secrets of the wind.',
    sockets: [{ socketID: 0, itemID: 3, gem: null, modifiers: null }],
    modifiers: [{ target: 'int', effects: [8] }, { target: 'maxMP', effects: [20] }],
    stackable: false,
  },
  {
    id: 4, name: 'Boots of Swiftness', image: '', imgThumb: '', weight: 8, itemId: 104,
    type: ObjectType.BOOTS, subtype: 'boots', maxSockets: 1,
    rarity: ObjectRarity.ENCHANTED, expireTick: null,
    implicit: [{ target: 'spd', effects: [5] }],
    affixPool: [],
    description: 'Light as air on your feet.',
    sockets: [],
    modifiers: [{ target: 'spd', effects: [12] }, { target: 'dodge', effects: [5] }],
    stackable: false,
  },
  {
    id: 5, name: 'Gauntlets of Might', image: '', imgThumb: '', weight: 12, itemId: 105,
    type: ObjectType.GLOVES, subtype: 'gloves', maxSockets: 1,
    rarity: ObjectRarity.COMMON, expireTick: null,
    implicit: [], affixPool: [],
    description: 'Sturdy iron gauntlets.',
    sockets: [],
    modifiers: [{ target: 'str', effects: [5] }],
    stackable: false,
  },
  {
    id: 6, name: 'Ring of Power', image: '', imgThumb: '', weight: 1, itemId: 106,
    type: ObjectType.RING, subtype: 'ring', maxSockets: 0,
    rarity: ObjectRarity.MYSTIC, expireTick: null,
    implicit: [{ target: 'int', effects: [2] }],
    affixPool: [],
    description: 'Ancient ring pulsing with arcane energy.',
    sockets: [],
    modifiers: [{ target: 'int', effects: [10] }, { target: 'crit', effects: [3] }],
    stackable: false,
  },
  {
    id: 7, name: 'Amulet of Vitality', image: '', imgThumb: '', weight: 2, itemId: 107,
    type: ObjectType.AMULET, subtype: 'amulet', maxSockets: 0,
    rarity: ObjectRarity.HEROIC, expireTick: null,
    implicit: [], affixPool: [],
    description: 'Grants the wearer immense vitality.',
    sockets: [],
    modifiers: [{ target: 'maxHP', effects: [150] }, { target: 'resist', effects: [10] }],
    stackable: false,
  },
  {
    id: 8, name: 'Health Potion', image: '', imgThumb: '', weight: 2, itemId: 201,
    type: ObjectType.CONSUMABLES, subtype: 'potion', maxSockets: 0,
    rarity: ObjectRarity.COMMON, expireTick: null,
    implicit: [], affixPool: [],
    description: 'Restores 100 HP.',
    sockets: [], modifiers: [], stackable: true, quantity: 5,
  },
  {
    id: 9, name: 'Mana Potion', image: '', imgThumb: '', weight: 2, itemId: 202,
    type: ObjectType.CONSUMABLES, subtype: 'potion', maxSockets: 0,
    rarity: ObjectRarity.COMMON, expireTick: null,
    implicit: [], affixPool: [],
    description: 'Restores 80 MP.',
    sockets: [], modifiers: [], stackable: true, quantity: 3,
  },
  {
    id: 10, name: 'Chaos Orb', image: '', imgThumb: '', weight: 1, itemId: 301,
    type: ObjectType.CHARM, subtype: 'charm', maxSockets: 0,
    rarity: ObjectRarity.GODLY, expireTick: null,
    implicit: [], affixPool: [],
    description: 'Reality bends around this orb.',
    sockets: [],
    modifiers: [{ target: 'str', effects: [20] }, { target: 'int', effects: [20] }, { target: 'dex', effects: [20] }],
    stackable: false,
  },
];

/** Demo gems */
export const createDemoGems = (): Gem[] => [
  { gemID: 1, name: 'Ruby of Strength', color: GemColor.RUBY, tier: 5, modifiers: [{ target: 'str', effects: [15] }], image: '/gem-ruby.png' },
  { gemID: 2, name: 'Sapphire of Wisdom', color: GemColor.SAPPHIRE, tier: 4, modifiers: [{ target: 'int', effects: [12] }], image: '/gem-sapphire.png' },
  { gemID: 3, name: 'Emerald of Agility', color: GemColor.EMERALD, tier: 6, modifiers: [{ target: 'dex', effects: [18] }], image: '/gem-emerald.png' },
  { gemID: 4, name: 'Amethyst of Power', color: GemColor.AMETHYST, tier: 7, modifiers: [{ target: 'crit', effects: [10] }], image: '/gem-amethyst.png' },
  { gemID: 5, name: 'Lesser Ruby', color: GemColor.RUBY, tier: 2, modifiers: [{ target: 'str', effects: [5] }], image: '/gem-ruby.png' },
  { gemID: 6, name: 'Greater Sapphire', color: GemColor.SAPPHIRE, tier: 8, modifiers: [{ target: 'int', effects: [20] }, { target: 'maxMP', effects: [50] }], image: '/gem-sapphire.png' },
];

/** Demo character stats */
export const createDemoStats = (): CharacterStats => ({
  hp: 450, mp: 280, maxHP: 650, maxMP: 380,
  str: 45, dex: 32, int: 28, spd: 25,
  def: 55, mdef: 30, crit: 15, acc: 88,
  dodge: 20, block: 35, resist: 25,
});

/** Demo inventory */
export const createDemoInventory = (): Inventory => ({
  id: 1, slotCount: 20, currentWeight: 75, maxWeight: 1000, nextAvailableSlot: 10,
  slots: [
    ...createDemoItems(),
    ...Array.from({ length: 10 }, (_, i) =>
      i < 4 ? createDemoItems()[i % createDemoItems().length] : null
    ),
    ...Array.from({ length: 6 }, () => null),
  ],
});

/** Equipment slot positions for paper doll (percentage-based) */
export const EQUIPMENT_SLOT_POSITIONS: { type: ObjectType; label: string; left: string; top: string; size: 'normal' | 'small' | 'wide' }[] = [
  { type: ObjectType.HELMET, label: 'Head', left: '50%', top: '2%', size: 'normal' },
  { type: ObjectType.AMULET, label: 'Neck', left: '68%', top: '14%', size: 'small' },
  { type: ObjectType.ARMOR, label: 'Body', left: '50%', top: '30%', size: 'wide' },
  { type: ObjectType.WEAPON, label: 'Weapon', left: '8%', top: '30%', size: 'normal' },
  { type: ObjectType.SHIELD, label: 'Off Hand', left: '82%', top: '30%', size: 'normal' },
  { type: ObjectType.GLOVES, label: 'Hands', left: '12%', top: '55%', size: 'normal' },
  { type: ObjectType.RING, label: 'Ring L', left: '28%', top: '58%', size: 'small' },
  { type: ObjectType.RING, label: 'Ring R', left: '72%', top: '58%', size: 'small' },
  { type: ObjectType.LEGGINGS, label: 'Legs', left: '50%', top: '58%', size: 'wide' },
  { type: ObjectType.BOOTS, label: 'Feet', left: '50%', top: '82%', size: 'normal' },
  { type: ObjectType.CHARM, label: 'Charm', left: '34%', top: '14%', size: 'small' },
];

export function useInventory() {
  const [inventory, setInventory] = useState<Inventory>(createDemoInventory());
  const [equipment, setEquipment] = useState<(Item | null)[]>(
    Array.from({ length: 12 }, () => null)
  );
  const [selectedItem, setSelectedItem] = useState<Item | null>(null);
  const [hoveredItem, setHoveredItem] = useState<Item | null>(null);
  const [socketTarget, setSocketTarget] = useState<Item | null>(null);

  /** Move item from inventory to equipment slot */
  const equipItem = useCallback((item: Item, slotIndex: number) => {
    setInventory(prev => {
      const newSlots = prev.slots.map(s => (s && s.id === item.id ? null : s));
      return { ...prev, slots: newSlots };
    });
    setEquipment(prev => {
      const newEquip = [...prev];
      newEquip[slotIndex] = item;
      return newEquip;
    });
  }, []);

  /** Unequip item back to inventory */
  const unequipItem = useCallback((slotIndex: number) => {
    setEquipment(prev => {
      const newEquip = [...prev];
      const item = newEquip[slotIndex];
      newEquip[slotIndex] = null;
      if (item) {
        setInventory(inv => {
          const emptyIndex = inv.slots.findIndex(s => !s);
          if (emptyIndex >= 0) {
            const newSlots = [...inv.slots];
            newSlots[emptyIndex] = item;
            return { ...inv, slots: newSlots };
          }
          return inv;
        });
      }
      return newEquip;
    });
  }, []);

  /** Move item between inventory slots */
  const moveInventoryItem = useCallback((fromIndex: number, toIndex: number) => {
    setInventory(prev => {
      const newSlots = [...prev.slots];
      const temp = newSlots[toIndex];
      newSlots[toIndex] = newSlots[fromIndex];
      newSlots[fromIndex] = temp;
      return { ...prev, slots: newSlots };
    });
  }, []);

  /** Swap equipped item with inventory item */
  const swapEquipInventory = useCallback((invIndex: number, equipIndex: number) => {
    setInventory(prev => {
      const newSlots = [...prev.slots];
      setEquipment(prevEquip => {
        const newEquip = [...prevEquip];
        const invItem = newSlots[invIndex];
        const equipItem = newEquip[equipIndex];
        newEquip[equipIndex] = invItem;
        newSlots[invIndex] = equipItem;
        return newEquip;
      });
      return { ...prev, slots: newSlots };
    });
  }, []);

  /** Socket a gem into an item */
  const socketGem = useCallback((itemId: number, socketId: number, gem: Gem) => {
    setEquipment(prev =>
      prev.map(item => {
        if (!item || item.id !== itemId) return item;
        const newSockets = item.sockets.map(s =>
          s.socketID === socketId ? { ...s, gem, modifiers: gem.modifiers[0] || null } : s
        );
        return { ...item, sockets: newSockets };
      })
    );
    setInventory(prev => ({
      ...prev,
      slots: prev.slots.map(item => {
        if (!item || item.id !== itemId) return item;
        const newSockets = item.sockets.map(s =>
          s.socketID === socketId ? { ...s, gem, modifiers: gem.modifiers[0] || null } : s
        );
        return { ...item, sockets: newSockets };
      }),
    }));
  }, []);

  /** Remove gem from socket */
  const removeGem = useCallback((itemId: number, socketId: number) => {
    setEquipment(prev =>
      prev.map(item => {
        if (!item || item.id !== itemId) return item;
        const newSockets = item.sockets.map(s =>
          s.socketID === socketId ? { ...s, gem: null, modifiers: null } : s
        );
        return { ...item, sockets: newSockets };
      })
    );
    setInventory(prev => ({
      ...prev,
      slots: prev.slots.map(item => {
        if (!item || item.id !== itemId) return item;
        const newSockets = item.sockets.map(s =>
          s.socketID === socketId ? { ...s, gem: null, modifiers: null } : s
        );
        return { ...item, sockets: newSockets };
      }),
    }));
  }, []);

  return {
    inventory,
    equipment,
    selectedItem,
    hoveredItem,
    socketTarget,
    setSelectedItem,
    setHoveredItem,
    setSocketTarget,
    equipItem,
    unequipItem,
    moveInventoryItem,
    swapEquipInventory,
    socketGem,
    removeGem,
  };
}
