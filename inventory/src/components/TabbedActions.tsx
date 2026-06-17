// ============================================
// Tabbed Actions Panel - Socketing, Imbuing, Crafting, Upgrading
// ============================================
import { useState } from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Gem, Hammer, FlaskConical, ArrowUpCircle, Sparkles, Plus, Minus, AlertCircle } from 'lucide-react';
import type { Item, Gem as GemType } from '@/types/game';
import { getGemColorHex, getGemImage, isEmptyItem } from '@/hooks/useInventory';

// ============================================
// Socketing Panel
// ============================================
interface SocketingPanelProps {
  selectedItem: Item | null;
  availableGems: GemType[];
  onSocketGem: (itemId: number, socketId: number, gem: GemType) => void;
  onRemoveGem: (itemId: number, socketId: number) => void;
}

function SocketingPanel({ selectedItem, availableGems, onSocketGem, onRemoveGem }: SocketingPanelProps) {
  const [selectedGem, setSelectedGem] = useState<GemType | null>(null);

  if (!selectedItem || isEmptyItem(selectedItem)) {
    return (
      <div className="flex flex-col items-center justify-center h-full text-stone-600 py-8">
        <Gem className="w-10 h-10 mb-3 opacity-30" />
        <p className="text-sm font-fantasy">Select an item with sockets</p>
        <p className="text-xs text-stone-700 mt-1">Click an item in your inventory or equipment to socket gems</p>
      </div>
    );
  }

  if (selectedItem.maxSockets === 0) {
    return (
      <div className="flex flex-col items-center justify-center h-full text-stone-600 py-8">
        <AlertCircle className="w-8 h-8 mb-3 opacity-40" />
        <p className="text-sm font-fantasy">{selectedItem.name} has no sockets</p>
        <p className="text-xs text-stone-700 mt-1">This item cannot hold gems</p>
      </div>
    );
  }

  return (
    <div className="flex flex-col h-full">
      {/* Selected item info */}
      <div className="flex items-center gap-3 mb-4 p-2 rounded bg-stone-900/50 border border-stone-800">
        <div className="w-10 h-10 rounded border border-amber-600/50 bg-stone-800 flex items-center justify-center">
          <Gem className="w-5 h-5 text-amber-600" />
        </div>
        <div>
          <p className="text-sm text-stone-200 font-fantasy">{selectedItem.name}</p>
          <p className="text-xs text-stone-500">{selectedItem.maxSockets} socket{selectedItem.maxSockets !== 1 ? 's' : ''}</p>
        </div>
      </div>

      {/* Sockets display */}
      <div className="mb-4">
        <p className="text-xs text-stone-500 uppercase tracking-wider mb-2">Sockets</p>
        <div className="flex gap-2">
          {selectedItem.sockets.map((socket, i) => (
            <div
              key={i}
              className={`relative w-12 h-12 rounded-lg border-2 flex items-center justify-center cursor-pointer transition-all ${
                socket.gem
                  ? 'border-amber-600 bg-amber-950/30'
                  : selectedGem
                    ? 'border-amber-600/50 bg-stone-900 hover:bg-amber-950/20'
                    : 'border-stone-700 bg-stone-900'
              }`}
              onClick={() => {
                if (selectedGem && !socket.gem) {
                  onSocketGem(selectedItem.id, socket.socketID, selectedGem);
                  setSelectedGem(null);
                } else if (socket.gem) {
                  onRemoveGem(selectedItem.id, socket.socketID);
                }
              }}
            >
              {socket.gem ? (
                <>
                  <img src={socket.gem.image || getGemImage(socket.gem.color)} alt={socket.gem.name} className="w-8 h-8 object-contain socket-gem" />
                  <button
                    className="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-900 border border-red-700 flex items-center justify-center hover:bg-red-700"
                    onClick={(e) => {
                      e.stopPropagation();
                      onRemoveGem(selectedItem.id, socket.socketID);
                    }}
                  >
                    <Minus className="w-2.5 h-2.5 text-red-300" />
                  </button>
                </>
              ) : selectedGem ? (
                <Plus className="w-5 h-5 text-amber-600/60" />
              ) : (
                <div className="w-4 h-4 rounded-full border border-stone-700 bg-stone-800" />
              )}
            </div>
          ))}
        </div>
      </div>

      {/* Available gems */}
      <div>
        <p className="text-xs text-stone-500 uppercase tracking-wider mb-2">Available Gems</p>
        <div className="grid grid-cols-3 gap-2">
          {availableGems.map((gem) => (
            <button
              key={gem.gemID}
              className={`flex flex-col items-center gap-1 p-2 rounded border transition-all ${
                selectedGem?.gemID === gem.gemID
                  ? 'border-amber-500 bg-amber-950/30'
                  : 'border-stone-800 bg-stone-900/50 hover:border-stone-600'
              }`}
              onClick={() => setSelectedGem(selectedGem?.gemID === gem.gemID ? null : gem)}
            >
              <img src={gem.image || getGemImage(gem.color)} alt={gem.name} className="w-8 h-8 object-contain" />
              <span className="text-[9px] text-stone-400 text-center leading-tight">{gem.name}</span>
              <span className="text-[8px] px-1 rounded" style={{ color: getGemColorHex(gem.color), backgroundColor: `${getGemColorHex(gem.color)}15` }}>
                T{gem.tier}
              </span>
            </button>
          ))}
        </div>
      </div>

      {selectedGem && (
        <div className="mt-3 p-2 rounded bg-amber-950/20 border border-amber-800/30">
          <p className="text-xs text-amber-500">
            Click an empty socket to insert <span className="font-semibold">{selectedGem.name}</span>
          </p>
        </div>
      )}
    </div>
  );
}

// ============================================
// Imbuing Panel
// ============================================
interface ImbuingPanelProps {
  selectedItem: Item | null;
}

function ImbuingPanel({ selectedItem }: ImbuingPanelProps) {
  const imbueOptions = [
    { name: 'Fire Essence', description: 'Add fire damage', cost: 500, color: '#dc2626' },
    { name: 'Ice Essence', description: 'Add cold damage', cost: 500, color: '#3b82f6' },
    { name: 'Lightning Essence', description: 'Add lightning damage', cost: 750, color: '#f59e0b' },
    { name: 'Void Essence', description: 'Add life steal', cost: 1000, color: '#a855f7' },
  ];

  if (!selectedItem || isEmptyItem(selectedItem)) {
    return (
      <div className="flex flex-col items-center justify-center h-full text-stone-600 py-8">
        <FlaskConical className="w-10 h-10 mb-3 opacity-30" />
        <p className="text-sm font-fantasy">Select an item to imbue</p>
      </div>
    );
  }

  return (
    <div className="flex flex-col h-full">
      <div className="flex items-center gap-3 mb-4 p-2 rounded bg-stone-900/50 border border-stone-800">
        <div className="w-10 h-10 rounded border border-purple-600/50 bg-stone-800 flex items-center justify-center">
          <FlaskConical className="w-5 h-5 text-purple-500" />
        </div>
        <div>
          <p className="text-sm text-stone-200 font-fantasy">{selectedItem.name}</p>
          <p className="text-xs text-stone-500">Imbue with magical essence</p>
        </div>
      </div>

      <div className="space-y-2">
        {imbueOptions.map((option) => (
          <button
            key={option.name}
            className="w-full flex items-center justify-between p-3 rounded border border-stone-800 bg-stone-900/40 hover:border-purple-700/50 hover:bg-purple-950/10 transition-all group"
          >
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 rounded-full flex items-center justify-center" style={{ backgroundColor: `${option.color}20` }}>
                <Sparkles className="w-4 h-4" style={{ color: option.color }} />
              </div>
              <div className="text-left">
                <p className="text-xs text-stone-300 font-medium">{option.name}</p>
                <p className="text-[10px] text-stone-500">{option.description}</p>
              </div>
            </div>
            <div className="flex items-center gap-1">
              <span className="text-xs text-amber-600">{option.cost}g</span>
            </div>
          </button>
        ))}
      </div>
    </div>
  );
}

// ============================================
// Crafting Panel
// ============================================
function CraftingPanel() {
  const recipes = [
    { name: 'Greater Health Potion', ingredients: ['Health Potion x3', 'Ruby Dust x1'], result: 'Restores 250 HP', cost: 100 },
    { name: 'Rune of Power', ingredients: ['Chaos Orb x1', 'Empty Rune x1'], result: '+15 STR', cost: 500 },
    { name: 'Enchanted Leather', ingredients: ['Leather x5', 'Mana Essence x2'], result: 'Crafting material', cost: 200 },
    { name: 'Flame Blade', ingredients: ['Iron Sword x1', 'Fire Gem x3'], result: 'Weapon with fire dmg', cost: 1000 },
  ];

  return (
    <div className="flex flex-col h-full">
      <p className="text-xs text-stone-500 uppercase tracking-wider mb-3">Available Recipes</p>
      <div className="space-y-2 overflow-y-auto max-h-[260px] pr-1">
        {recipes.map((recipe) => (
          <div
            key={recipe.name}
            className="p-3 rounded border border-stone-800 bg-stone-900/40 hover:border-stone-600 transition-all"
          >
            <div className="flex items-center justify-between mb-2">
              <p className="text-xs text-stone-300 font-fantasy">{recipe.name}</p>
              <span className="text-[10px] text-amber-600">{recipe.cost}g</span>
            </div>
            <div className="space-y-0.5 mb-2">
              {recipe.ingredients.map((ing, i) => (
                <p key={i} className="text-[10px] text-stone-500 flex items-center gap-1">
                  <span className="w-1 h-1 rounded-full bg-stone-600" />
                  {ing}
                </p>
              ))}
            </div>
            <div className="flex items-center gap-1 pt-1 border-t border-stone-800">
              <Hammer className="w-3 h-3 text-stone-500" />
              <span className="text-[10px] text-emerald-500">{recipe.result}</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

// ============================================
// Upgrading Panel
// ============================================
interface UpgradingPanelProps {
  selectedItem: Item | null;
}

function UpgradingPanel({ selectedItem }: UpgradingPanelProps) {
  const upgradeTiers = [
    { level: '+1', cost: 100, spindels: 5, success: '95%' },
    { level: '+2', cost: 250, spindels: 10, success: '90%' },
    { level: '+3', cost: 500, spindels: 20, success: '80%' },
    { level: '+4', cost: 1000, spindels: 40, success: '65%' },
    { level: '+5', cost: 2500, spindels: 80, success: '45%' },
  ];

  if (!selectedItem || isEmptyItem(selectedItem)) {
    return (
      <div className="flex flex-col items-center justify-center h-full text-stone-600 py-8">
        <ArrowUpCircle className="w-10 h-10 mb-3 opacity-30" />
        <p className="text-sm font-fantasy">Select an item to upgrade</p>
      </div>
    );
  }

  return (
    <div className="flex flex-col h-full">
      <div className="flex items-center gap-3 mb-4 p-2 rounded bg-stone-900/50 border border-stone-800">
        <div className="w-10 h-10 rounded border border-emerald-600/50 bg-stone-800 flex items-center justify-center">
          <ArrowUpCircle className="w-5 h-5 text-emerald-500" />
        </div>
        <div>
          <p className="text-sm text-stone-200 font-fantasy">{selectedItem.name}</p>
          <p className="text-xs text-stone-500">Enhance item power</p>
        </div>
      </div>

      <div className="space-y-1.5 overflow-y-auto max-h-[260px] pr-1">
        {upgradeTiers.map((tier) => (
          <button
            key={tier.level}
            className="w-full flex items-center justify-between p-2.5 rounded border border-stone-800 bg-stone-900/40 hover:border-emerald-700/50 hover:bg-emerald-950/10 transition-all"
          >
            <div className="flex items-center gap-3">
              <span className="text-sm font-fantasy text-emerald-500">{tier.level}</span>
              <div className="text-left">
                <div className="flex items-center gap-2">
                  <span className="text-[10px] text-amber-600">{tier.cost}g</span>
                  <span className="text-[10px] text-purple-500">{tier.spindels}s</span>
                </div>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <span className="text-[10px] text-stone-500">Success: {tier.success}</span>
            </div>
          </button>
        ))}
      </div>
    </div>
  );
}

// ============================================
// Main Tabbed Actions Component
// ============================================
interface TabbedActionsProps {
  selectedItem: Item | null;
  availableGems: GemType[];
  onSocketGem: (itemId: number, socketId: number, gem: GemType) => void;
  onRemoveGem: (itemId: number, socketId: number) => void;
}

export function TabbedActions({
  selectedItem,
  availableGems,
  onSocketGem,
  onRemoveGem,
}: TabbedActionsProps) {
  return (
    <div className="h-full flex flex-col">
      <Tabs defaultValue="socketing" className="flex-1 flex flex-col">
        <TabsList className="w-full grid grid-cols-4 bg-stone-900/80 border border-stone-800 p-0.5 h-9">
          <TabsTrigger
            value="socketing"
            className="fantasy-tab text-[10px] uppercase tracking-wider data-[state=active]:bg-stone-800 data-[state=active]:text-amber-500 data-[state=active]:shadow-none rounded"
          >
            <Gem className="w-3 h-3 mr-1" />
            Socket
          </TabsTrigger>
          <TabsTrigger
            value="imbuing"
            className="fantasy-tab text-[10px] uppercase tracking-wider data-[state=active]:bg-stone-800 data-[state=active]:text-purple-400 data-[state=active]:shadow-none rounded"
          >
            <FlaskConical className="w-3 h-3 mr-1" />
            Imbue
          </TabsTrigger>
          <TabsTrigger
            value="crafting"
            className="fantasy-tab text-[10px] uppercase tracking-wider data-[state=active]:bg-stone-800 data-[state=active]:text-stone-300 data-[state=active]:shadow-none rounded"
          >
            <Hammer className="w-3 h-3 mr-1" />
            Craft
          </TabsTrigger>
          <TabsTrigger
            value="upgrading"
            className="fantasy-tab text-[10px] uppercase tracking-wider data-[state=active]:bg-stone-800 data-[state=active]:text-emerald-500 data-[state=active]:shadow-none rounded"
          >
            <ArrowUpCircle className="w-3 h-3 mr-1" />
            Upgrade
          </TabsTrigger>
        </TabsList>

        <div className="flex-1 mt-2 p-3 rounded-lg border border-stone-800/80 bg-stone-950/50 overflow-hidden">
          <TabsContent value="socketing" className="mt-0 h-full">
            <SocketingPanel
              selectedItem={selectedItem}
              availableGems={availableGems}
              onSocketGem={onSocketGem}
              onRemoveGem={onRemoveGem}
            />
          </TabsContent>
          <TabsContent value="imbuing" className="mt-0 h-full">
            <ImbuingPanel selectedItem={selectedItem} />
          </TabsContent>
          <TabsContent value="crafting" className="mt-0 h-full">
            <CraftingPanel />
          </TabsContent>
          <TabsContent value="upgrading" className="mt-0 h-full">
            <UpgradingPanel selectedItem={selectedItem} />
          </TabsContent>
        </div>
      </Tabs>
    </div>
  );
}
