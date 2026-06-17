// ============================================
// Draggable Item Component
// Renders an item that can be dragged
// ============================================
import { useDraggable } from '@dnd-kit/core';
import { CSS } from '@dnd-kit/utilities';
import { getRarityColor, getRarityGlow, isEmptyItem } from '@/hooks/useInventory';
import type { Item } from '@/types/game';
import { ItemTooltip } from './ItemTooltip';
import { useState, useCallback } from 'react';

interface DraggableItemProps {
  item: Item | null;
  index: number;
  sourceType: 'inventory' | 'equipment';
  onHover?: (item: Item | null) => void;
  onClick?: (item: Item) => void;
  size?: 'normal' | 'small';
}

// Item type icon mapping using SVG
function ItemTypeIcon({ type, rarityColor }: { type: string; rarityColor: string }) {
  const iconProps = { width: 24, height: 24, stroke: rarityColor, fill: 'none', strokeWidth: 1.5 };

  switch (type) {
    case 'WEAPON':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <path d="M14.5 17.5L3 6V3h3l11.5 11.5" /><path d="M13 19l6-6" /><path d="M16 16l4 4" /><path d="M19 21l2-2" />
        </svg>
      );
    case 'ARMOR':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <path d="M12 2L4 6v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V6l-8-4z" />
        </svg>
      );
    case 'HELMET':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <path d="M4 16v-4a8 8 0 1116 0v4" /><path d="M2 16h20v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2z" />
        </svg>
      );
    case 'BOOTS':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <path d="M4 18h14l2-6h-6V6a2 2 0 00-2-2H8v10H4v4z" />
        </svg>
      );
    case 'GLOVES':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <path d="M8 13V4.5a2.5 2.5 0 015 0V13" /><path d="M8 9h6.5a2.5 2.5 0 012.5 2.5V13" /><path d="M4 13h16v4a4 4 0 01-4 4H8a4 4 0 01-4-4v-4z" />
        </svg>
      );
    case 'RING':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="8" />
        </svg>
      );
    case 'AMULET':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <path d="M6 3h12" /><path d="M12 3v10" /><circle cx="12" cy="16" r="5" />
        </svg>
      );
    case 'SHIELD':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
        </svg>
      );
    case 'CONSUMABLES':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <path d="M9 2v2" /><path d="M15 2v2" /><path d="M12 2v8" /><path d="M8 10h8v10a2 2 0 01-2 2h-4a2 2 0 01-2-2V10z" />
        </svg>
      );
    case 'CHARM':
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <path d="M12 3l1.5 4.5h4.5l-3.5 2.5 1.5 4.5-4-3-4 3 1.5-4.5L6 7.5h4.5z" />
        </svg>
      );
    default:
      return (
        <svg {...iconProps} viewBox="0 0 24 24">
          <rect x="3" y="3" width="18" height="18" rx="2" />
        </svg>
      );
  }
}

export function DraggableItem({
  item,
  index,
  sourceType,
  onHover,
  onClick,
  size = 'normal',
}: DraggableItemProps) {
  const [showTooltip, setShowTooltip] = useState(false);
  const [tooltipPos, setTooltipPos] = useState({ x: 0, y: 0 });

  const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({
    id: `${sourceType}-${index}`,
    data: { item, index, sourceType },
    disabled: !item || isEmptyItem(item),
  });

  const handleMouseEnter = useCallback(() => {
    if (item && !isEmptyItem(item)) {
      setShowTooltip(true);
      onHover?.(item);
    }
  }, [item, onHover]);

  const handleMouseLeave = useCallback(() => {
    setShowTooltip(false);
    onHover?.(null);
  }, [onHover]);

  const handleMouseMove = useCallback((e: React.MouseEvent) => {
    const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
    setTooltipPos({
      x: rect.left + rect.width + 8,
      y: rect.top,
    });
  }, []);

  const handleClick = useCallback(() => {
    if (item && !isEmptyItem(item) && onClick) {
      onClick(item);
    }
  }, [item, onClick]);

  if (!item || isEmptyItem(item)) {
    return (
      <div
        ref={setNodeRef}
        className={`inventory-slot empty-slot-pattern rounded border border-stone-800/60 ${
          size === 'small' ? 'w-12 h-12' : 'w-14 h-14'
        }`}
      />
    );
  }

  const style = {
    transform: CSS.Translate.toString(transform),
    zIndex: isDragging ? 1000 : 'auto',
    opacity: isDragging ? 0.6 : 1,
  };

  const rarityColor = getRarityColor(item.rarity as Item['rarity']);
  const glow = getRarityGlow(item.rarity as Item['rarity']);

  return (
    <>
      <div
        ref={setNodeRef}
        {...listeners}
        {...attributes}
        style={style}
        className={`inventory-slot relative rounded border-2 cursor-grab active:cursor-grabbing flex items-center justify-center ${
          size === 'small' ? 'w-12 h-12' : 'w-14 h-14'
        } ${isDragging ? 'dragging-item' : ''}`}
        onMouseEnter={handleMouseEnter}
        onMouseLeave={handleMouseLeave}
        onMouseMove={handleMouseMove}
        onClick={handleClick}
        data-item-id={item.id}
      >
        {/* Background with rarity tint */}
        <div
          className="absolute inset-0 rounded opacity-10"
          style={{ backgroundColor: rarityColor }}
        />

        {/* Glow effect */}
        {glow !== 'none' && (
          <div
            className="absolute inset-0 rounded pointer-events-none"
            style={{ boxShadow: glow }}
          />
        )}

        {/* Item icon */}
        <div className="relative z-10">
          <ItemTypeIcon type={item.type} rarityColor={rarityColor} />
        </div>

        {/* Rarity border */}
        <div
          className="absolute inset-0 rounded pointer-events-none"
          style={{ borderColor: rarityColor, borderWidth: '1px', opacity: 0.5 }}
        />

        {/* Quantity badge for stackables */}
        {item.stackable && item.quantity && item.quantity > 1 && (
          <span className="quantity-badge">{item.quantity}</span>
        )}

        {/* Socket indicators */}
        {item.maxSockets > 0 && (
          <div className="absolute bottom-0.5 left-0.5 flex gap-0.5 z-20">
            {item.sockets.map((socket, i) => (
              <div
                key={i}
                className="w-1.5 h-1.5 rounded-full"
                style={
                  socket.gem
                    ? { backgroundColor: '#d97706', boxShadow: '0 0 2px #d97706' }
                    : { backgroundColor: '#292524', border: '1px solid #44403c' }
                }
              />
            ))}
          </div>
        )}
      </div>

      {/* Tooltip portal */}
      {showTooltip && (
        <div
          className="fixed z-[9999]"
          style={{
            left: tooltipPos.x,
            top: tooltipPos.y,
          }}
        >
          <ItemTooltip item={item} />
        </div>
      )}
    </>
  );
}
