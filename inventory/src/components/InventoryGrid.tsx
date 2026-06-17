// ============================================
// Inventory Grid - 20-slot drag-and-drop grid
// ============================================
import { useDroppable } from '@dnd-kit/core';
import type { Item } from '@/types/game';
import { DraggableItem } from './DraggableItem';
import { Backpack } from 'lucide-react';

interface InventoryGridProps {
  slots: (Item | null)[];
  currentWeight: number;
  maxWeight: number;
  onHover?: (item: Item | null) => void;
  onClick?: (item: Item) => void;
}

function InventorySlot({
  item,
  index,
  onHover,
  onClick,
}: {
  item: Item | null;
  index: number;
  onHover?: (item: Item | null) => void;
  onClick?: (item: Item) => void;
}) {
  const { isOver, setNodeRef } = useDroppable({
    id: `inventory-${index}`,
    data: { index, accepts: ['inventory', 'equipment'] },
  });

  return (
    <div
      ref={setNodeRef}
      className={`inventory-slot rounded border transition-all duration-200 w-14 h-14 ${
        isOver
          ? 'border-amber-500 bg-amber-500/10 shadow-[inset_0_0_20px_rgba(217,119,6,0.4)]'
          : 'border-stone-800/60 bg-stone-900/40 empty-slot-pattern'
      }`}
    >
      <DraggableItem
        item={item}
        index={index}
        sourceType="inventory"
        onHover={onHover}
        onClick={onClick}
        size="normal"
      />
    </div>
  );
}

export function InventoryGrid({ slots, currentWeight, maxWeight, onHover, onClick }: InventoryGridProps) {
  const weightPercent = (currentWeight / maxWeight) * 100;
  const weightColor = weightPercent > 90 ? 'text-red-500' : weightPercent > 70 ? 'text-amber-500' : 'text-stone-400';

  return (
    <div className="flex flex-col h-full">
      {/* Header */}
      <div className="flex items-center justify-between mb-3 px-1">
        <div className="flex items-center gap-2">
          <Backpack className="w-4 h-4 text-amber-600" />
          <h3 className="font-fantasy text-sm text-stone-300 tracking-wide">Inventory</h3>
        </div>
        <div className={`text-xs font-mono ${weightColor}`}>
          {currentWeight} / {maxWeight}
        </div>
      </div>

      {/* Weight bar */}
      <div className="w-full h-1 bg-stone-800 rounded-full mb-3 overflow-hidden">
        <div
          className="h-full rounded-full transition-all duration-500"
          style={{
            width: `${weightPercent}%`,
            backgroundColor: weightPercent > 90 ? '#dc2626' : weightPercent > 70 ? '#d97706' : '#57534e',
          }}
        />
      </div>

      {/* Grid */}
      <div className="grid grid-cols-4 gap-1.5">
        {Array.from({ length: 20 }, (_, i) => (
          <InventorySlot
            key={i}
            item={slots[i] || null}
            index={i}
            onHover={onHover}
            onClick={onClick}
          />
        ))}
      </div>
    </div>
  );
}
