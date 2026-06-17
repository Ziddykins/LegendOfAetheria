// ============================================
// Item Slot - Drop target for drag-and-drop
// ============================================
import { useDroppable } from '@dnd-kit/core';
import { type Item, type ObjectType } from '@/types/game';
import { DraggableItem } from './DraggableItem';
import { canEquipInSlot } from '@/hooks/useInventory';
import { Sword, Shield, Footprints, HardHat, Hand, Sparkles, Gem, FlaskConical, Scroll, Wand2 } from 'lucide-react';

interface ItemSlotProps {
  item: Item | null;
  index: number;
  slotType: ObjectType;
  label: string;
  onHover?: (item: Item | null) => void;
  onClick?: (item: Item) => void;
  size?: 'normal' | 'small' | 'wide';
}

// Inline ring icon since lucide doesn't export one
function RingIcon({ className }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
      <circle cx="12" cy="12" r="8" />
    </svg>
  );
}

const slotIcons: Record<string, React.ReactNode> = {
  'WEAPON': <Sword className="w-4 h-4 text-stone-600" />,
  'ARMOR': <Shield className="w-4 h-4 text-stone-600" />,
  'HELMET': <HardHat className="w-4 h-4 text-stone-600" />,
  'BOOTS': <Footprints className="w-4 h-4 text-stone-600" />,
  'GLOVES': <Hand className="w-4 h-4 text-stone-600" />,
  'RING': <RingIcon className="w-4 h-4 text-stone-600" />,
  'AMULET': <Gem className="w-4 h-4 text-stone-600" />,
  'CHARM': <Sparkles className="w-4 h-4 text-stone-600" />,
  'CONSUMABLES': <FlaskConical className="w-4 h-4 text-stone-600" />,
  'QUEST': <Scroll className="w-4 h-4 text-stone-600" />,
  'SHIELD': <Shield className="w-4 h-4 text-stone-600" />,
  'WINGS': <Wand2 className="w-4 h-4 text-stone-600" />,
  'LEGGINGS': <Shield className="w-4 h-4 text-stone-600" />,
};

export function ItemSlot({
  item,
  index,
  slotType,
  label,
  onHover,
  onClick,
  size = 'normal',
}: ItemSlotProps) {
  const { isOver, setNodeRef, active } = useDroppable({
    id: `equipment-${index}`,
    data: { slotType, index, accepts: [slotType] },
    disabled: false,
  });

  // Check if the dragged item can be equipped here
  const draggedItem = active?.data?.current?.item as Item | undefined;
  const canAccept = draggedItem ? canEquipInSlot(draggedItem, slotType) : true;
  const isValidDrop = isOver && canAccept;

  const sizeClasses = {
    normal: 'w-14 h-14',
    small: 'w-11 h-11',
    wide: 'w-16 h-14',
  };

  return (
    <div className="flex flex-col items-center gap-0.5">
      <div
        ref={setNodeRef}
        className={`equipment-slot relative rounded-lg border transition-all duration-200 ${sizeClasses[size]} ${
          isValidDrop
            ? 'border-amber-500 bg-amber-500/10 shadow-[inset_0_0_20px_rgba(217,119,6,0.4)]'
            : isOver && !canAccept
              ? 'border-red-500 bg-red-500/10'
              : item
                ? 'border-stone-600/80 bg-stone-900/50'
                : 'border-stone-700/60 bg-stone-900/30 empty-slot-pattern'
        }`}
      >
        {/* Slot label overlay when empty */}
        {!item && (
          <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
            {slotIcons[slotType] || <Sparkles className="w-4 h-4 text-stone-700" />}
          </div>
        )}

        {/* Draggable item inside slot */}
        <DraggableItem
          item={item}
          index={index}
          sourceType="equipment"
          onHover={onHover}
          onClick={onClick}
          size={size === 'small' ? 'small' : 'normal'}
        />
      </div>

      {/* Slot label */}
      <span className="text-[9px] text-stone-600 uppercase tracking-wider font-medium">
        {label}
      </span>
    </div>
  );
}
