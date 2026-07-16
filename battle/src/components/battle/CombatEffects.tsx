import { useRef, useEffect } from 'react';
import type { SpellEffectData } from '@/types/battle';

interface CombatEffectsProps {
  damageNumbers: never[];
  spellEffects: SpellEffectData[];
  onDamageComplete: (id: string) => void;
  onSpellComplete: (id: string) => void;
}

function SpellFlash({ effect, onComplete }: { effect: SpellEffectData; onComplete: (id: string) => void }) {
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const timer = setTimeout(() => {
      onComplete(effect.id);
    }, 400);
    return () => clearTimeout(timer);
  }, [effect.id, onComplete]);

  const flashClass =
    effect.type === 'fire'
      ? 'spell-fire-flash'
      : effect.type === 'ice'
      ? 'spell-ice-flash'
      : 'spell-heal-flash';

  return (
    <div
      ref={ref}
      className={`absolute inset-0 pointer-events-none z-40 ${flashClass}`}
    />
  );
}

export function CombatEffects({
  spellEffects,
  onSpellComplete,
}: CombatEffectsProps) {
  return (
    <div className="absolute inset-0 pointer-events-none overflow-hidden z-40">
      {/* Slash effect line - shown during attack animation */}
      <div
        className="absolute hidden"
        style={{
          top: '20%',
          left: '10%',
          width: '80%',
          height: 2,
          background: 'linear-gradient(90deg, transparent, #fff, var(--crimson-glow), transparent)',
          boxShadow: '0 0 8px var(--crimson-glow)',
          transform: 'rotate(-30deg)',
          transformOrigin: 'left center',
        }}
      />

      {/* Spell effects */}
      {spellEffects.map((effect) => (
        <SpellFlash key={effect.id} effect={effect} onComplete={onSpellComplete} />
      ))}

      {/* Frost crack effect */}
      {spellEffects.some((e) => e.type === 'ice') && (
        <svg
          className="absolute inset-0 w-full h-full pointer-events-none"
          style={{ opacity: 0.6 }}
        >
          <defs>
            <filter id="glow">
              <feGaussianBlur stdDeviation="2" result="coloredBlur" />
              <feMerge>
                <feMergeNode in="coloredBlur" />
                <feMergeNode in="SourceGraphic" />
              </feMerge>
            </filter>
          </defs>
          <g filter="url(#glow)" stroke="rgba(68, 170, 255, 0.8)" strokeWidth="1.5" fill="none">
            <path d="M50,80 L70,75 L85,85 L95,70" />
            <path d="M60,120 L75,110 L90,115 L100,100" />
            <path d="M80,60 L95,55 L110,65" />
          </g>
        </svg>
      )}
    </div>
  );
}
