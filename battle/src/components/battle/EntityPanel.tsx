import { useMemo } from 'react';
import { Shield, Swords, Skull, Activity, Droplets, Zap } from 'lucide-react';
import type { EntityStats } from '@/types/battle';

interface EntityPanelProps {
  name: string;
  level: number;
  stats: EntityStats;
  isPlayer: boolean;
  isActive: boolean;
  flashType: 'damage' | 'heal' | null;
}

export function EntityPanel({
  name,
  level,
  stats,
  isPlayer,
  isActive,
  flashType,
}: EntityPanelProps) {
  const hpPercent = useMemo(
    () => (stats.maxHP > 0 ? (stats.hp / stats.maxHP) * 100 : 0),
    [stats.hp, stats.maxHP]
  );
  const mpPercent = useMemo(
    () => (stats.maxMP > 0 ? (stats.mp / stats.maxMP) * 100 : 0),
    [stats.mp, stats.maxMP]
  );
  const epPercent = useMemo(
    () => (stats.maxEP > 0 ? (stats.ep / stats.maxEP) * 100 : 0),
    [stats.ep, stats.maxEP]
  );

  const isLowHP = hpPercent < 25;
  const isDead = stats.hp <= 0;

  const flashClass = flashType === 'damage'
    ? 'flash-damage'
    : flashType === 'heal'
    ? 'flash-heal'
    : '';

  const activeClass = isActive
    ? isPlayer
      ? 'player-active player-active-pulse'
      : 'enemy-active enemy-active-pulse'
    : '';

  return (
    <div
      className={`entity-card ${activeClass} ${flashClass} relative p-4 w-full`}
      style={{ opacity: isDead ? 0.6 : 1 }}
    >
      {/* Avatar and Name Row */}
      <div className="flex items-center gap-3 mb-3">
        <div
          className="w-16 h-16 rounded-lg flex items-center justify-center"
          style={{
            background: isPlayer
              ? 'linear-gradient(135deg, rgba(34,102,204,0.2), rgba(34,102,204,0.05))'
              : 'linear-gradient(135deg, rgba(204,34,34,0.2), rgba(204,34,34,0.05))',
            border: `1px solid ${isPlayer ? 'rgba(34,102,204,0.3)' : 'rgba(204,34,34,0.3)'}`,
          }}
        >
          {isPlayer ? (
            <Shield className="w-8 h-8" style={{ color: 'var(--mana-blue)' }} />
          ) : (
            <Skull className="w-8 h-8" style={{ color: 'var(--crimson-glow)' }} />
          )}
        </div>
        <div className="flex-1 min-w-0">
          <h3
            className="font-cinzel text-sm font-semibold uppercase tracking-wider truncate"
            style={{
              color: isPlayer ? 'var(--text-primary)' : 'var(--crimson-glow)',
            }}
          >
            {name}
          </h3>
          <span
            className="text-xs font-medium uppercase tracking-widest"
            style={{ color: 'var(--text-muted)' }}
          >
            Lv. {level}
          </span>
        </div>
      </div>

      {/* HP Bar */}
      <div className="mb-2">
        <div className="flex justify-between items-center mb-1">
          <div className="flex items-center gap-1">
            <Activity className="w-3 h-3" style={{ color: 'var(--health-red)' }} />
            <span
              className="text-xs font-medium uppercase tracking-wider"
              style={{ color: 'var(--text-muted)' }}
            >
              HP
            </span>
          </div>
          <span
            className={`font-mono-stats text-xs font-bold ${isLowHP ? 'low-hp-pulse' : ''}`}
            style={{ color: isLowHP ? 'var(--crimson-glow)' : 'var(--text-primary)' }}
          >
            {stats.hp} / {stats.maxHP}
          </span>
        </div>
        <div className="stat-bar-track">
          <div
            className={`stat-bar-fill-hp ${isLowHP ? 'low-hp-pulse' : ''}`}
            style={{ width: `${hpPercent}%` }}
          />
        </div>
      </div>

      {/* MP Bar */}
      <div className="mb-2">
        <div className="flex justify-between items-center mb-1">
          <div className="flex items-center gap-1">
            <Droplets className="w-3 h-3" style={{ color: 'var(--mana-blue)' }} />
            <span
              className="text-xs font-medium uppercase tracking-wider"
              style={{ color: 'var(--text-muted)' }}
            >
              MP
            </span>
          </div>
          <span
            className="font-mono-stats text-xs font-bold"
            style={{ color: 'var(--text-primary)' }}
          >
            {stats.mp} / {stats.maxMP}
          </span>
        </div>
        <div className="stat-bar-track">
          <div className="stat-bar-fill-mp" style={{ width: `${mpPercent}%` }} />
        </div>
      </div>

      {/* EP Bar */}
      <div className="mb-3">
        <div className="flex justify-between items-center mb-1">
          <div className="flex items-center gap-1">
            <Zap className="w-3 h-3" style={{ color: 'var(--energy-amber)' }} />
            <span
              className="text-xs font-medium uppercase tracking-wider"
              style={{ color: 'var(--text-muted)' }}
            >
              EP
            </span>
          </div>
          <span
            className="font-mono-stats text-xs font-bold"
            style={{ color: 'var(--text-primary)' }}
          >
            {stats.ep} / {stats.maxEP}
          </span>
        </div>
        <div className="stat-bar-track">
          <div className="stat-bar-fill-ep" style={{ width: `${epPercent}%` }} />
        </div>
      </div>

      {/* Stats Row */}
      <div
        className="flex justify-between pt-2 mt-2"
        style={{ borderTop: '1px solid var(--border-subtle)' }}
      >
        <div className="flex items-center gap-1.5">
          <Swords className="w-3.5 h-3.5" style={{ color: 'var(--text-muted)' }} />
          <span className="text-xs uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>
            STR
          </span>
          <span className="font-mono-stats text-sm font-bold" style={{ color: 'var(--text-primary)' }}>
            {stats.str}
          </span>
        </div>
        <div className="flex items-center gap-1.5">
          <Shield className="w-3.5 h-3.5" style={{ color: 'var(--text-muted)' }} />
          <span className="text-xs uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>
            DEF
          </span>
          <span className="font-mono-stats text-sm font-bold" style={{ color: 'var(--text-primary)' }}>
            {stats.def}
          </span>
        </div>
      </div>
    </div>
  );
}
