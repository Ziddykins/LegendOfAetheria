// ============================================
// Character Paper Doll - Character silhouette with equipment slots
// Diablo 2 style anatomical positioning
// ============================================
import type { Item, ObjectType } from '@/types/game';
import { ItemSlot } from './ItemSlot';

interface CharacterPaperDollProps {
  equipment: (Item | null)[];
  onHover?: (item: Item | null) => void;
  onClick?: (item: Item) => void;
}

// Equipment slot definitions with anatomical positioning
const EQUIPMENT_LAYOUT: {
  type: ObjectType;
  label: string;
  position: { left: string; top: string };
  size: 'normal' | 'small' | 'wide';
  slotIndex: number;
}[] = [
  // Head
  { type: 'HELMET' as ObjectType, label: 'Head', position: { left: '50%', top: '2%' }, size: 'normal', slotIndex: 0 },
  // Neck
  { type: 'AMULET' as ObjectType, label: 'Neck', position: { left: '66%', top: '15%' }, size: 'small', slotIndex: 1 },
  // Body
  { type: 'ARMOR' as ObjectType, label: 'Body', position: { left: '50%', top: '30%' }, size: 'wide', slotIndex: 2 },
  // Main Hand
  { type: 'WEAPON' as ObjectType, label: 'Weapon', position: { left: '8%', top: '30%' }, size: 'normal', slotIndex: 3 },
  // Off Hand
  { type: 'SHIELD' as ObjectType, label: 'Off Hand', position: { left: '85%', top: '30%' }, size: 'normal', slotIndex: 4 },
  // Hands
  { type: 'GLOVES' as ObjectType, label: 'Hands', position: { left: '12%', top: '55%' }, size: 'normal', slotIndex: 5 },
  // Ring Left
  { type: 'RING' as ObjectType, label: 'Ring', position: { left: '30%', top: '62%' }, size: 'small', slotIndex: 6 },
  // Ring Right
  { type: 'RING' as ObjectType, label: 'Ring', position: { left: '70%', top: '62%' }, size: 'small', slotIndex: 7 },
  // Legs
  { type: 'LEGGINGS' as ObjectType, label: 'Legs', position: { left: '50%', top: '58%' }, size: 'wide', slotIndex: 8 },
  // Feet
  { type: 'BOOTS' as ObjectType, label: 'Feet', position: { left: '50%', top: '82%' }, size: 'normal', slotIndex: 9 },
  // Charm
  { type: 'CHARM' as ObjectType, label: 'Charm', position: { left: '34%', top: '15%' }, size: 'small', slotIndex: 10 },
];

export function CharacterPaperDoll({ equipment, onHover, onClick }: CharacterPaperDollProps) {
  return (
    <div className="relative w-full h-full min-h-[480px] flex items-center justify-center">
      {/* Background glow behind character */}
      <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div className="w-[200px] h-[380px] bg-amber-900/5 rounded-full blur-3xl" />
      </div>

      {/* Character Silhouette */}
      <div className="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
        <img
          src="/character-silhouette.png"
          alt="Character"
          className="h-[420px] opacity-60 object-contain"
          draggable={false}
        />
      </div>

      {/* Equipment Slots positioned around the character */}
      {EQUIPMENT_LAYOUT.map((slot) => (
        <div
          key={slot.slotIndex}
          className="absolute z-10"
          style={{
            left: slot.position.left,
            top: slot.position.top,
            transform: 'translate(-50%, -50%)',
          }}
        >
          <ItemSlot
            item={equipment[slot.slotIndex]}
            index={slot.slotIndex}
            slotType={slot.type}
            label={slot.label}
            onHover={onHover}
            onClick={onClick}
            size={slot.size}
          />
        </div>
      ))}
    </div>
  );
}
