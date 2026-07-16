// ============================================
// Item Tooltip - Hover tooltip with rarity styling
// ============================================
import { getRarityColor } from '@/hooks/useInventory';
import type { Item } from '@/types/game';
import { ObjectType } from '@/types/game';
import { Sword, Shield, Footprints, HardHat, Hand, Sparkles, Gem, FlaskConical, Scroll } from 'lucide-react';

interface ItemTooltipProps {
  item: Item;
}

// Inline ring icon
function RingIcon({ className }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
      <circle cx="12" cy="12" r="8" />
    </svg>
  );
}

const typeIcons: Record<string, React.ReactNode> = {
  [ObjectType.WEAPON]: <Sword className="w-3.5 h-3.5" />,
  [ObjectType.ARMOR]: <Shield className="w-3.5 h-3.5" />,
  [ObjectType.HELMET]: <HardHat className="w-3.5 h-3.5" />,
  [ObjectType.BOOTS]: <Footprints className="w-3.5 h-3.5" />,
  [ObjectType.GLOVES]: <Hand className="w-3.5 h-3.5" />,
  [ObjectType.RING]: <RingIcon className="w-3.5 h-3.5" />,
  [ObjectType.AMULET]: <Gem className="w-3.5 h-3.5" />,
  [ObjectType.CHARM]: <Sparkles className="w-3.5 h-3.5" />,
  [ObjectType.CONSUMABLES]: <FlaskConical className="w-3.5 h-3.5" />,
  [ObjectType.QUEST]: <Scroll className="w-3.5 h-3.5" />,
  [ObjectType.SHIELD]: <Shield className="w-3.5 h-3.5" />,
};

export function ItemTooltip({ item }: ItemTooltipProps) {
  const rarityColor = getRarityColor(item.rarity);

  return (
    <div className="item-tooltip p-3 min-w-[220px] max-w-[280px] pointer-events-none">
      {/* Item Name */}
      <div className="font-fantasy text-sm font-bold mb-1" style={{ color: rarityColor }}>
        {item.name}
      </div>

      {/* Type & Rarity */}
      <div className="flex items-center gap-1.5 mb-2">
        <span className="text-stone-500">{typeIcons[item.type]}</span>
        <span className="text-xs text-stone-400 uppercase tracking-wider">{item.subtype}</span>
        <span className="text-stone-600 mx-1">|</span>
        <span className="text-xs font-semibold uppercase tracking-wider" style={{ color: rarityColor }}>
          {item.rarity}
        </span>
      </div>

      {/* Divider */}
      <div className="border-b border-stone-800 mb-2" />

      {/* Description */}
      {item.description && (
        <p className="text-xs text-stone-400 italic mb-2 leading-relaxed">
          {item.description}
        </p>
      )}

      {/* Modifiers */}
      {item.modifiers.length > 0 && (
        <div className="mb-2">
          {item.modifiers.map((mod, i) => (
            <div key={i} className="flex items-center gap-1 text-xs">
              <span className="text-amber-600">+</span>
              <span className="text-emerald-400 font-medium">
                {mod.effects.reduce((a, b) => a + b, 0)}
              </span>
              <span className="text-stone-400 capitalize">{mod.target}</span>
            </div>
          ))}
        </div>
      )}

      {/* Sockets */}
      {item.maxSockets > 0 && (
        <div className="flex items-center gap-1 mt-1.5">
          <span className="text-xs text-stone-500">Sockets:</span>
          <div className="flex gap-1">
            {Array.from({ length: item.maxSockets }, (_, i) => (
              <div
                key={i}
                className="w-3 h-3 rounded-full border border-stone-700"
                style={
                  item.sockets[i]?.gem
                    ? { backgroundColor: '#d97706', boxShadow: '0 0 4px #d97706' }
                    : { backgroundColor: '#1c1917' }
                }
              />
            ))}
          </div>
        </div>
      )}

      {/* Weight */}
      <div className="mt-2 text-xs text-stone-600">
        Weight: {item.weight}
      </div>

      {/* Stack quantity */}
      {item.stackable && item.quantity && item.quantity > 1 && (
        <div className="text-xs text-stone-500">
          Quantity: {item.quantity}
        </div>
      )}
    </div>
  );
}
