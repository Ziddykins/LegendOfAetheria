// ============================================
// Stats Panel - Character stats display
// ============================================
import type { CharacterStats } from '@/types/game';
import { Heart, Droplets, Sword, Shield, Wind, Target, Zap, Eye, Footprints, Dumbbell, Brain, Sparkles } from 'lucide-react';

interface StatsPanelProps {
  stats: CharacterStats;
  equippedBonus?: Partial<CharacterStats>;
}

interface StatRowProps {
  icon: React.ReactNode;
  label: string;
  value: number;
  maxValue?: number;
  color: string;
  bonus?: number;
}

function StatRow({ icon, label, value, maxValue, color, bonus }: StatRowProps) {
  const showBar = maxValue !== undefined;
  const percent = showBar ? (value / (maxValue || 1)) * 100 : 0;

  return (
    <div className="flex flex-col gap-0.5">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-1.5">
          <span style={{ color }}>{icon}</span>
          <span className="text-[11px] text-stone-400 uppercase tracking-wider">{label}</span>
        </div>
        <div className="flex items-center gap-1">
          <span className="text-xs font-mono text-stone-200">{value}</span>
          {showBar && <span className="text-[10px] text-stone-600">/ {maxValue}</span>}
          {bonus !== undefined && bonus > 0 && (
            <span className="text-[10px] text-emerald-500 font-medium">+{bonus}</span>
          )}
        </div>
      </div>
      {showBar && (
        <div className="w-full h-1 bg-stone-800 rounded-full overflow-hidden">
          <div
            className="h-full rounded-full transition-all duration-500"
            style={{ width: `${Math.min(percent, 100)}%`, backgroundColor: color }}
          />
        </div>
      )}
    </div>
  );
}

export function StatsPanel({ stats, equippedBonus = {} }: StatsPanelProps) {
  const getBonus = (key: keyof CharacterStats): number => {
    return (equippedBonus[key] as number) || 0;
  };

  return (
    <div className="flex flex-col gap-3">
      {/* Header */}
      <div className="flex items-center gap-2 mb-1">
        <Sparkles className="w-4 h-4 text-amber-600" />
        <h3 className="font-fantasy text-sm text-stone-300 tracking-wide">Attributes</h3>
      </div>

      {/* Core Stats - HP/MP */}
      <div className="space-y-2">
        <StatRow
          icon={<Heart className="w-3.5 h-3.5" />}
          label="Health"
          value={stats.hp}
          maxValue={stats.maxHP}
          color="#dc2626"
          bonus={getBonus('maxHP')}
        />
        <StatRow
          icon={<Droplets className="w-3.5 h-3.5" />}
          label="Mana"
          value={stats.mp}
          maxValue={stats.maxMP}
          color="#3b82f6"
          bonus={getBonus('maxMP')}
        />
      </div>

      {/* Divider */}
      <div className="border-b border-stone-800" />

      {/* Primary Stats */}
      <div className="space-y-1.5">
        <StatRow icon={<Sword className="w-3.5 h-3.5" />} label="STR" value={stats.str} color="#d97706" bonus={getBonus('str')} />
        <StatRow icon={<Wind className="w-3.5 h-3.5" />} label="DEX" value={stats.dex} color="#22c55e" bonus={getBonus('dex')} />
        <StatRow icon={<Brain className="w-3.5 h-3.5" />} label="INT" value={stats.int} color="#3b82f6" bonus={getBonus('int')} />
        <StatRow icon={<Footprints className="w-3.5 h-3.5" />} label="SPD" value={stats.spd} color="#f59e0b" bonus={getBonus('spd')} />
      </div>

      {/* Divider */}
      <div className="border-b border-stone-800" />

      {/* Combat Stats */}
      <div className="space-y-1.5">
        <StatRow icon={<Shield className="w-3.5 h-3.5" />} label="DEF" value={stats.def} color="#57534e" bonus={getBonus('def')} />
        <StatRow icon={<Sparkles className="w-3.5 h-3.5" />} label="MDEF" value={stats.mdef} color="#a855f7" bonus={getBonus('mdef')} />
        <StatRow icon={<Target className="w-3.5 h-3.5" />} label="CRIT" value={stats.crit} color="#ef4444" bonus={getBonus('crit')} />
        <StatRow icon={<Eye className="w-3.5 h-3.5" />} label="ACC" value={stats.acc} color="#e7e5e4" />
        <StatRow icon={<Wind className="w-3.5 h-3.5" />} label="DODGE" value={stats.dodge} color="#14b8a6" bonus={getBonus('dodge')} />
        <StatRow icon={<Dumbbell className="w-3.5 h-3.5" />} label="BLOCK" value={stats.block} color="#78716c" bonus={getBonus('block')} />
        <StatRow icon={<Zap className="w-3.5 h-3.5" />} label="RESIST" value={stats.resist} color="#fbbf24" bonus={getBonus('resist')} />
      </div>
    </div>
  );
}
