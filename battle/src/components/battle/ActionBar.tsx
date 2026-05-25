import { useState, useRef, useCallback } from 'react';
import { Swords, Flame, Snowflake, HeartPlus, ChevronDown, Loader2 } from 'lucide-react';
import type { CombatAction, ActionType } from '@/types/battle';

interface ActionBarProps {
  isEnabled: boolean;
  isProcessing: boolean;
  hasEP: boolean;
  playerHP: number;
  playerMaxHP: number;
  onAction: (action: CombatAction) => void;
}

const ATTACK_TYPES = [
  { name: 'Quick Strike', description: 'Fast attack with moderate damage' },
  { name: 'Heavy Blow', description: 'Slow but powerful strike' },
  { name: 'Precise Strike', description: 'Accurate attack, harder to block' },
];

export function ActionBar({
  isEnabled,
  isProcessing,
  hasEP,
  playerHP,
  playerMaxHP,
  onAction,
}: ActionBarProps) {
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const [selectedAttack, setSelectedAttack] = useState(ATTACK_TYPES[0].name);
  const [ripples, setRipples] = useState<{ id: string; x: number; y: number }[]>([]);
  const attackBtnRef = useRef<HTMLButtonElement>(null);

  const handleAction = useCallback(
    (type: ActionType, subtype?: string) => {
      if (!isEnabled || isProcessing) return;

      // Add ripple effect
      if (attackBtnRef.current) {
        const rect = attackBtnRef.current.getBoundingClientRect();
        const id = Math.random().toString(36).substr(2, 9);
        setRipples((prev) => [...prev, { id, x: rect.width / 2, y: rect.height / 2 }]);
        setTimeout(() => setRipples((prev) => prev.filter((r) => r.id !== id)), 300);
      }

      onAction({ type, subtype });
      setDropdownOpen(false);
    },
    [isEnabled, isProcessing, onAction]
  );

  const handleAttackSelect = useCallback(
    (attackName: string) => {
      setSelectedAttack(attackName);
      setDropdownOpen(false);
      handleAction('attack', attackName);
    },
    [handleAction]
  );

  const buttonsDisabled = !isEnabled || isProcessing || !hasEP;
  const healDisabled = buttonsDisabled || playerHP >= playerMaxHP;

  return (
    <div className="relative flex items-center gap-2 px-4 py-3" style={{ minHeight: 64 }}>
      {/* Attack Button with Dropdown */}
      <div className="relative flex-1">
        <button
          ref={attackBtnRef}
          disabled={buttonsDisabled}
          onClick={() => handleAction('attack', selectedAttack)}
          className="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-md transition-all duration-150 relative overflow-hidden"
          style={{
            background: buttonsDisabled
              ? 'rgba(26, 26, 37, 0.5)'
              : 'rgba(26, 26, 37, 0.9)',
            border: `1px solid ${buttonsDisabled ? 'var(--border-subtle)' : 'var(--border-focus)'}`,
            opacity: buttonsDisabled ? 0.4 : 1,
            cursor: buttonsDisabled ? 'not-allowed' : 'pointer',
          }}
        >
          {ripples.map((ripple) => (
            <span
              key={ripple.id}
              className="ripple"
              style={{
                left: ripple.x - 20,
                top: ripple.y - 20,
                width: 40,
                height: 40,
              }}
            />
          ))}
          {isProcessing ? (
            <Loader2 className="w-5 h-5 spinner" style={{ color: 'var(--crimson-glow)' }} />
          ) : (
            <>
              <Swords className="w-5 h-5" style={{ color: 'var(--crimson-glow)' }} />
              <span
                className="font-cinzel text-sm font-semibold uppercase tracking-wider"
                style={{ color: 'var(--text-primary)' }}
              >
                Attack
              </span>
            </>
          )}
        </button>

        {/* Dropdown Toggle */}
        <button
          disabled={buttonsDisabled}
          onClick={() => setDropdownOpen(!dropdownOpen)}
          className="absolute right-0 top-0 bottom-0 px-2 rounded-r-md transition-all duration-150"
          style={{
            background: 'transparent',
            borderLeft: `1px solid ${buttonsDisabled ? 'var(--border-subtle)' : 'var(--border-focus)'}`,
            opacity: buttonsDisabled ? 0.4 : 1,
            cursor: buttonsDisabled ? 'not-allowed' : 'pointer',
          }}
        >
          <ChevronDown
            className="w-4 h-4 transition-transform duration-200"
            style={{
              color: 'var(--text-muted)',
              transform: dropdownOpen ? 'rotate(180deg)' : 'rotate(0deg)',
            }}
          />
        </button>

        {/* Attack Type Dropdown */}
        {dropdownOpen && (
          <div
            className="absolute top-full left-0 right-0 mt-1 rounded-md overflow-hidden z-50"
            style={{
              background: 'var(--surface-raised)',
              border: '1px solid var(--border-subtle)',
              boxShadow: '0 8px 32px rgba(0,0,0,0.5)',
            }}
          >
            {ATTACK_TYPES.map((attack) => (
              <button
                key={attack.name}
                onClick={() => handleAttackSelect(attack.name)}
                className="w-full text-left px-3 py-2.5 transition-colors duration-150 hover:bg-[var(--surface-hover)]"
                style={{
                  borderBottom: '1px solid var(--border-subtle)',
                }}
              >
                <div
                  className="font-cinzel text-xs font-semibold uppercase tracking-wider"
                  style={{ color: 'var(--text-primary)' }}
                >
                  {attack.name}
                </div>
                <div className="text-xs mt-0.5" style={{ color: 'var(--text-muted)' }}>
                  {attack.description}
                </div>
              </button>
            ))}
          </div>
        )}
      </div>

      {/* Burn Button */}
      <button
        disabled={buttonsDisabled}
        onClick={() => handleAction('burn')}
        className="flex items-center justify-center gap-2 px-4 py-3 rounded-md transition-all duration-150"
        style={{
          background: buttonsDisabled ? 'rgba(26, 26, 37, 0.5)' : 'rgba(26, 26, 37, 0.9)',
          border: `1px solid ${buttonsDisabled ? 'var(--border-subtle)' : 'rgba(255, 68, 0, 0.4)'}`,
          opacity: buttonsDisabled ? 0.4 : 1,
          cursor: buttonsDisabled ? 'not-allowed' : 'pointer',
          boxShadow: !buttonsDisabled ? '0 0 12px rgba(255, 68, 0, 0.1)' : 'none',
        }}
        onMouseEnter={(e) => {
          if (!buttonsDisabled) {
            e.currentTarget.style.borderColor = 'rgba(255, 68, 0, 0.6)';
            e.currentTarget.style.boxShadow = '0 0 12px rgba(255, 68, 0, 0.2)';
          }
        }}
        onMouseLeave={(e) => {
          e.currentTarget.style.borderColor = buttonsDisabled
            ? 'var(--border-subtle)'
            : 'rgba(255, 68, 0, 0.4)';
          e.currentTarget.style.boxShadow = !buttonsDisabled
            ? '0 0 12px rgba(255, 68, 0, 0.1)'
            : 'none';
        }}
      >
        <Flame className="w-5 h-5" style={{ color: 'var(--spell-fire)' }} />
        <span
          className="font-cinzel text-sm font-semibold uppercase tracking-wider hidden sm:inline"
          style={{ color: 'var(--text-primary)' }}
        >
          Burn
        </span>
      </button>

      {/* Frost Button */}
      <button
        disabled={buttonsDisabled}
        onClick={() => handleAction('frost')}
        className="flex items-center justify-center gap-2 px-4 py-3 rounded-md transition-all duration-150"
        style={{
          background: buttonsDisabled ? 'rgba(26, 26, 37, 0.5)' : 'rgba(26, 26, 37, 0.9)',
          border: `1px solid ${buttonsDisabled ? 'var(--border-subtle)' : 'rgba(68, 170, 255, 0.4)'}`,
          opacity: buttonsDisabled ? 0.4 : 1,
          cursor: buttonsDisabled ? 'not-allowed' : 'pointer',
          boxShadow: !buttonsDisabled ? '0 0 12px rgba(68, 170, 255, 0.1)' : 'none',
        }}
        onMouseEnter={(e) => {
          if (!buttonsDisabled) {
            e.currentTarget.style.borderColor = 'rgba(68, 170, 255, 0.6)';
            e.currentTarget.style.boxShadow = '0 0 12px rgba(68, 170, 255, 0.2)';
          }
        }}
        onMouseLeave={(e) => {
          e.currentTarget.style.borderColor = buttonsDisabled
            ? 'var(--border-subtle)'
            : 'rgba(68, 170, 255, 0.4)';
          e.currentTarget.style.boxShadow = !buttonsDisabled
            ? '0 0 12px rgba(68, 170, 255, 0.1)'
            : 'none';
        }}
      >
        <Snowflake className="w-5 h-5" style={{ color: 'var(--spell-ice)' }} />
        <span
          className="font-cinzel text-sm font-semibold uppercase tracking-wider hidden sm:inline"
          style={{ color: 'var(--text-primary)' }}
        >
          Frost
        </span>
      </button>

      {/* Heal Button */}
      <button
        disabled={healDisabled}
        onClick={() => handleAction('heal')}
        className="flex items-center justify-center gap-2 px-4 py-3 rounded-md transition-all duration-150"
        style={{
          background: healDisabled ? 'rgba(26, 26, 37, 0.5)' : 'rgba(26, 26, 37, 0.9)',
          border: `1px solid ${healDisabled ? 'var(--border-subtle)' : 'rgba(68, 204, 102, 0.4)'}`,
          opacity: healDisabled ? 0.4 : 1,
          cursor: healDisabled ? 'not-allowed' : 'pointer',
          boxShadow: !healDisabled ? '0 0 12px rgba(68, 204, 102, 0.1)' : 'none',
        }}
        onMouseEnter={(e) => {
          if (!healDisabled) {
            e.currentTarget.style.borderColor = 'rgba(68, 204, 102, 0.6)';
            e.currentTarget.style.boxShadow = '0 0 12px rgba(68, 204, 102, 0.2)';
          }
        }}
        onMouseLeave={(e) => {
          e.currentTarget.style.borderColor = healDisabled
            ? 'var(--border-subtle)'
            : 'rgba(68, 204, 102, 0.4)';
          e.currentTarget.style.boxShadow = !healDisabled
            ? '0 0 12px rgba(68, 204, 102, 0.1)'
            : 'none';
        }}
      >
        <HeartPlus className="w-5 h-5" style={{ color: 'var(--spell-heal)' }} />
        <span
          className="font-cinzel text-sm font-semibold uppercase tracking-wider hidden sm:inline"
          style={{ color: 'var(--text-primary)' }}
        >
          Heal
        </span>
      </button>
    </div>
  );
}
