import { useRef, useEffect } from 'react';
import type { CombatLogEntry } from '@/types/battle';

interface BattleLogProps {
  entries: CombatLogEntry[];
}

function getEntryColor(style: CombatLogEntry['style']): string {
  switch (style) {
    case 'player':
      return 'var(--text-primary)';
    case 'enemy':
      return 'var(--crimson-glow)';
    case 'system':
      return 'var(--text-gold)';
    case 'victory':
      return 'var(--success-green)';
    case 'defeat':
      return 'var(--health-red)';
    default:
      return 'var(--text-primary)';
  }
}

function getEntryFontSize(style: CombatLogEntry['style']): string {
  switch (style) {
    case 'victory':
      return '15px';
    case 'defeat':
      return '15px';
    default:
      return '13px';
  }
}

function getEntryFontWeight(style: CombatLogEntry['style']): string {
  switch (style) {
    case 'victory':
    case 'defeat':
      return '600';
    default:
      return '400';
  }
}

export function BattleLog({ entries }: BattleLogProps) {
  const scrollRef = useRef<HTMLDivElement>(null);

  // Auto-scroll to newest entry
  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollTop = 0;
    }
  }, [entries.length]);

  return (
    <div
      ref={scrollRef}
      className="battle-log-container px-4 py-3 overflow-y-auto"
      style={{
        maxHeight: 200,
        minHeight: 120,
        background: 'rgba(10, 10, 15, 0.9)',
        border: '1px solid rgba(42, 42, 58, 0.4)',
        borderRadius: 8,
      }}
      role="log"
      aria-live="polite"
      aria-label="Battle log"
    >
      {entries.length === 0 ? (
        <div
          className="text-center py-8 italic"
          style={{ color: 'var(--text-muted)' }}
        >
          Battle begins... Choose your action.
        </div>
      ) : (
        entries.map((entry) => (
          <div
            key={entry.id}
            className="log-entry py-1 leading-relaxed"
            style={{
              color: getEntryColor(entry.style),
              fontSize: getEntryFontSize(entry.style),
              fontWeight: getEntryFontWeight(entry.style),
              borderBottom: '1px solid rgba(42, 42, 58, 0.2)',
            }}
          >
            {entry.text}
          </div>
        ))
      )}
    </div>
  );
}
