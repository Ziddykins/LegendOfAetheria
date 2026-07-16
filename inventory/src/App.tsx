// ============================================
// Legend of Aetheria - Inventory System
// Main App Component
// ============================================
import { useState, useCallback, useMemo } from 'react';
import {
  DndContext,
  DragOverlay,
  PointerSensor,
  useSensor,
  useSensors,
  type DragEndEvent,
  type DragStartEvent,
  type DragOverEvent,
} from '@dnd-kit/core';
import { CharacterPaperDoll } from '@/components/CharacterPaperDoll';
import { InventoryGrid } from '@/components/InventoryGrid';
import { StatsPanel } from '@/components/StatsPanel';
import { TabbedActions } from '@/components/TabbedActions';
import { DraggableItem } from '@/components/DraggableItem';
import {
  useInventory,
  createDemoStats,
  createDemoGems,
  calculateEquippedStats,
  canEquipInSlot,
} from '@/hooks/useInventory';
import type { Item } from '@/types/game';
import { Crown, Coins, Sparkles, User } from 'lucide-react';

function App() {
  const {
    inventory,
    equipment,
    selectedItem,
    setSelectedItem,
    setHoveredItem,
    equipItem,
    unequipItem,
    moveInventoryItem,
    swapEquipInventory,
    socketGem,
    removeGem,
  } = useInventory();

  const [activeDragItem, setActiveDragItem] = useState<Item | null>(null);

  const sensors = useSensors(
    useSensor(PointerSensor, {
      activationConstraint: { distance: 5 },
    })
  );

  const stats = useMemo(() => createDemoStats(), []);
  const gems = useMemo(() => createDemoGems(), []);
  const equippedBonus = useMemo(() => calculateEquippedStats(equipment), [equipment]);

  // Character info
  const character = {
    name: 'Aethon the Reborn',
    level: 42,
    gold: 15420,
    spindels: 350,
  };

  // Handle drag start
  const handleDragStart = useCallback((event: DragStartEvent) => {
    const { active } = event;
    const data = active.data.current;
    if (data?.item) {
      setActiveDragItem(data.item);
    }
  }, []);

  // Handle drag end
  const handleDragEnd = useCallback(
    (event: DragEndEvent) => {
      const { active, over } = event;
      setActiveDragItem(null);

      if (!over) return;

      const activeData = active.data.current;
      const overData = over.data.current;
      if (!activeData || !overData) return;

      const sourceType = activeData.sourceType as 'inventory' | 'equipment';
      const sourceIndex = activeData.index as number;
      const draggedItem = activeData.item as Item;

      // Dropping on equipment slot
      if (over.id.toString().startsWith('equipment-')) {
        const targetSlotType = overData.slotType;
        const targetIndex = overData.index as number;

        if (!canEquipInSlot(draggedItem, targetSlotType)) return;

        if (sourceType === 'inventory') {
          // If slot has item, swap
          if (equipment[targetIndex]) {
            swapEquipInventory(sourceIndex, targetIndex);
          } else {
            equipItem(draggedItem, targetIndex);
          }
        } else if (sourceType === 'equipment') {
          // Swap between equipment slots
          if (sourceIndex !== targetIndex) {
            // Create temp swap would need local state for this
          }
        }
      }

      // Dropping on inventory slot
      if (over.id.toString().startsWith('inventory-')) {
        const targetIndex = overData.index as number;

        if (sourceType === 'inventory' && sourceIndex !== targetIndex) {
          moveInventoryItem(sourceIndex, targetIndex);
        } else if (sourceType === 'equipment') {
          // Unequip to inventory
          unequipItem(sourceIndex);
        }
      }
    },
    [equipment, equipItem, unequipItem, moveInventoryItem, swapEquipInventory]
  );

  const handleDragOver = useCallback((_event: DragOverEvent) => {
    // Visual feedback handled by droppable hooks
  }, []);

  const handleDragCancel = useCallback(() => {
    setActiveDragItem(null);
  }, []);

  const handleItemClick = useCallback((item: Item) => {
    setSelectedItem(item);
  }, [setSelectedItem]);

  return (
    <div
      className="min-h-screen w-full text-stone-200 overflow-hidden"
      style={{
        background: `
          linear-gradient(180deg, rgba(12,10,9,0.97) 0%, rgba(12,10,9,0.95) 100%),
          url('/bg-texture.jpg')
        `,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundAttachment: 'fixed',
      }}
    >
      <DndContext
        sensors={sensors}
        onDragStart={handleDragStart}
        onDragEnd={handleDragEnd}
        onDragOver={handleDragOver}
        onDragCancel={handleDragCancel}
      >
        {/* Top Bar */}
        <header className="h-12 border-b border-stone-800/80 bg-stone-950/90 backdrop-blur-sm flex items-center px-4 gap-4">
          <div className="flex items-center gap-2">
            <Crown className="w-5 h-5 text-amber-600" />
            <h1 className="font-fantasy text-lg text-amber-500 tracking-wider">Legend of Aetheria</h1>
          </div>
          <div className="w-px h-6 bg-stone-800" />
          <div className="flex items-center gap-2">
            <User className="w-4 h-4 text-stone-500" />
            <span className="text-sm text-stone-300">{character.name}</span>
            <span className="text-xs text-amber-600 font-fantasy">Lv. {character.level}</span>
          </div>
          <div className="ml-auto flex items-center gap-4">
            <div className="flex items-center gap-1.5">
              <Coins className="w-4 h-4 text-yellow-600" />
              <span className="text-sm font-mono text-yellow-500">{character.gold.toLocaleString()}</span>
            </div>
            <div className="flex items-center gap-1.5">
              <Sparkles className="w-4 h-4 text-purple-500" />
              <span className="text-sm font-mono text-purple-400">{character.spindels}</span>
            </div>
          </div>
        </header>

        {/* Main Content */}
        <main className="h-[calc(100vh-48px)] p-3 flex gap-3">
          {/* Left Column - Stats */}
          <aside className="w-[180px] flex-shrink-0 rounded-lg border border-stone-800/80 bg-stone-950/60 backdrop-blur-sm p-3 overflow-y-auto">
            <StatsPanel stats={stats} equippedBonus={equippedBonus} />
          </aside>

          {/* Center Column - Character Paper Doll */}
          <section className="flex-1 rounded-lg border border-stone-800/80 bg-stone-950/60 backdrop-blur-sm p-3 min-w-0">
            <CharacterPaperDoll
              equipment={equipment}
              onHover={setHoveredItem}
              onClick={handleItemClick}
            />
          </section>

          {/* Right Column - Inventory + Tabs */}
          <aside className="w-[280px] flex-shrink-0 flex flex-col gap-3">
            {/* Inventory Grid */}
            <div className="rounded-lg border border-stone-800/80 bg-stone-950/60 backdrop-blur-sm p-3">
              <InventoryGrid
                slots={inventory.slots}
                currentWeight={inventory.currentWeight}
                maxWeight={inventory.maxWeight}
                onHover={setHoveredItem}
                onClick={handleItemClick}
              />
            </div>

            {/* Tabbed Actions */}
            <div className="flex-1 rounded-lg border border-stone-800/80 bg-stone-950/60 backdrop-blur-sm p-3 min-h-0 overflow-hidden">
              <TabbedActions
                selectedItem={selectedItem}
                availableGems={gems}
                onSocketGem={socketGem}
                onRemoveGem={removeGem}
              />
            </div>
          </aside>
        </main>

        {/* Drag Overlay */}
        <DragOverlay dropAnimation={null}>
          {activeDragItem && (
            <div className="opacity-80 scale-105 pointer-events-none">
              <DraggableItem
                item={activeDragItem}
                index={-1}
                sourceType="inventory"
                size="normal"
              />
            </div>
          )}
        </DragOverlay>
      </DndContext>
    </div>
  );
}

export default App;
