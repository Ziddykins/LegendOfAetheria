import { create } from 'zustand';
import api from '../api/client';
import { useUIStore } from './useUIStore';

let savedChar = null;
try {
  savedChar = JSON.parse(localStorage.getItem('loa_active_char') || 'null');
} catch {
  savedChar = null;
}

export const useCharacterStore = create((set, get) => ({
  activeCharacter: savedChar,
  characterSlots: [
    {
      slot: 1,
      id: 1,
      name: 'Aelric Stormborn',
      race: 'Elf',
      level: 14,
      avatar: 'avatar-1.webp',
      location: 'Valensgard Citadel',
      x: 12,
      y: 45,
      floor: 3,
      gold: 4250,
      alignment: 78,
      dateCreated: '2026-03-12',
      vitals: {
        hp: 340,
        maxHp: 340,
        mp: 180,
        maxMp: 220,
        ep: 90,
        maxEp: 100,
      },
      stats: {
        str: 18,
        int: 34,
        def: 22,
        agi: 26,
        luc: 15,
        ap: 4,
      },
      equipment: {
        head: { name: 'Circlet of the Arcane', rarity: 'rare', icon: 'auto_awesome' },
        chest: { name: 'Robe of Celestial Weave', rarity: 'epic', icon: 'shield' },
        mainHand: { name: 'Sunforged Wand', rarity: 'legendary', icon: 'colorize' },
        offHand: { name: 'Tome of Astral Light', rarity: 'rare', icon: 'menu_book' },
        legs: { name: 'Silken Trousers', rarity: 'common', icon: 'styler' },
        feet: { name: 'Boots of Swift Strides', rarity: 'rare', icon: 'snowshoeing' },
        amulet: { name: 'Pendant of Aetheria', rarity: 'legendary', icon: 'diamond' },
        ring: { name: 'Band of Clarity', rarity: 'epic', icon: 'radio_button_checked' },
      },
    },
    {
      slot: 2,
      id: 2,
      name: 'Brog Ironbreaker',
      race: 'Dwarf',
      level: 9,
      avatar: 'avatar-4.webp',
      location: 'Deepstone Caverns',
      x: 4,
      y: 19,
      floor: 2,
      gold: 1820,
      alignment: 12,
      dateCreated: '2026-03-15',
      vitals: {
        hp: 510,
        maxHp: 510,
        mp: 40,
        maxMp: 40,
        ep: 110,
        maxEp: 120,
      },
      stats: {
        str: 38,
        int: 10,
        def: 42,
        agi: 14,
        luc: 12,
        ap: 0,
      },
      equipment: {
        head: { name: 'Dwarven War Helm', rarity: 'rare', icon: 'sports_motorsports' },
        chest: { name: 'Reinforced Adamantite Plate', rarity: 'epic', icon: 'shield' },
        mainHand: { name: 'Earthshaker Battleaxe', rarity: 'epic', icon: 'handyman' },
        offHand: { name: 'Iron Tower Shield', rarity: 'rare', icon: 'security' },
        legs: { name: 'Plated Greaves', rarity: 'rare', icon: 'styler' },
        feet: { name: 'Steelcapped Boots', rarity: 'common', icon: 'snowshoeing' },
        amulet: { name: 'Talisman of Stone', rarity: 'common', icon: 'diamond' },
        ring: { name: 'Iron Signet', rarity: 'common', icon: 'radio_button_checked' },
      },
    },
    {
      slot: 3,
      id: null, // Empty slot
      name: null,
    },
  ],
  isLoading: false,

  fetchCharacters: async () => {
    set({ isLoading: true });
    try {
      const response = await api.get('/v1/characters');
      if (response.data && Array.isArray(response.data)) {
        // If API returns characters, update slots
      }
    } catch (err) {
      // Retain seeded slot data if backend is not yet populated
    } finally {
      set({ isLoading: false });
    }
  },

  selectCharacter: (character) => {
    localStorage.setItem('loa_active_char', JSON.stringify(character));
    set({ activeCharacter: character });
    useUIStore.getState().notify(`Entering Aetheria as ${character.name}...`, 'success');
  },

  allocateAP: (statKey) => {
    const { activeCharacter } = get();
    if (!activeCharacter || activeCharacter.stats.ap <= 0) return;

    const updatedChar = {
      ...activeCharacter,
      stats: {
        ...activeCharacter.stats,
        [statKey]: (activeCharacter.stats[statKey] || 0) + 1,
        ap: activeCharacter.stats.ap - 1,
      },
    };

    localStorage.setItem('loa_active_char', JSON.stringify(updatedChar));
    set({ activeCharacter: updatedChar });
    useUIStore.getState().notify(`Allocated 1 AP to ${statKey.toUpperCase()}`, 'info');
  },

  createCharacter: async (slotNumber, charData) => {
    const newChar = {
      slot: slotNumber,
      id: Date.now(),
      name: charData.name,
      race: charData.race,
      avatar: charData.avatar || 'avatar-2.webp',
      level: 1,
      location: 'Valensgard Citadel',
      x: 0,
      y: 0,
      floor: 1,
      gold: 150,
      alignment: 50,
      dateCreated: new Date().toISOString().split('T')[0],
      vitals: {
        hp: 100 + charData.def * 10,
        maxHp: 100 + charData.def * 10,
        mp: 50 + charData.int * 8,
        maxMp: 50 + charData.int * 8,
        ep: 100,
        maxEp: 100,
      },
      stats: {
        str: charData.str,
        int: charData.int,
        def: charData.def,
        agi: 10,
        luc: 10,
        ap: 0,
      },
      equipment: {
        head: null,
        chest: { name: 'Apprentice Tunic', rarity: 'common', icon: 'shield' },
        mainHand: { name: 'Novice Staff', rarity: 'common', icon: 'colorize' },
        offHand: null,
        legs: { name: 'Worn Breeches', rarity: 'common', icon: 'styler' },
        feet: { name: 'Travelers Boots', rarity: 'common', icon: 'snowshoeing' },
        amulet: null,
        ring: null,
      },
    };

    const slots = [...get().characterSlots];
    const index = slots.findIndex((s) => s.slot === slotNumber);
    if (index !== -1) {
      slots[index] = newChar;
    }

    set({ characterSlots: slots });
    useUIStore.getState().notify(`Character ${charData.name} created!`, 'success');
    return newChar;
  },
}));
