import { useRef, useEffect } from 'react';
import gsap from 'gsap';
import { Trophy, RotateCcw, Skull } from 'lucide-react';
import type { BattleResult } from '@/types/battle';

interface VictoryOverlayProps {
  result: BattleResult;
  onContinue: () => void;
}

export function VictoryOverlay({ result, onContinue }: VictoryOverlayProps) {
  const overlayRef = useRef<HTMLDivElement>(null);
  const textRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!textRef.current) return;

    gsap.fromTo(
      textRef.current,
      { scale: 0.5, opacity: 0 },
      { scale: 1, opacity: 1, duration: 0.6, ease: 'back.out(1.7)' }
    );

    if (overlayRef.current) {
      gsap.fromTo(
        overlayRef.current,
        { opacity: 0 },
        { opacity: 1, duration: 0.4 }
      );
    }
  }, []);

  if (result === 'ongoing') return null;

  const isVictory = result === 'victory';

  return (
    <div
      ref={overlayRef}
      className="absolute inset-0 z-50 flex items-center justify-center"
      style={{
        background: isVictory
          ? 'radial-gradient(circle, rgba(40, 167, 69, 0.15) 0%, rgba(10, 10, 15, 0.8) 100%)'
          : 'radial-gradient(circle, rgba(139, 26, 26, 0.2) 0%, rgba(10, 10, 15, 0.85) 100%)',
        backdropFilter: 'blur(2px)',
      }}
    >
      <div ref={textRef} className="text-center">
        {/* Icon */}
        <div className="mb-4 flex justify-center">
          {isVictory ? (
            <Trophy
              className="w-16 h-16"
              style={{ color: 'var(--text-gold)', filter: 'drop-shadow(0 0 12px rgba(212,168,48,0.5))' }}
            />
          ) : (
            <Skull
              className="w-16 h-16"
              style={{ color: 'var(--crimson-glow)', filter: 'drop-shadow(0 0 12px rgba(204,34,34,0.5))' }}
            />
          )}
        </div>

        {/* Title */}
        <h2
          className={`font-cinzel text-5xl font-bold mb-3 ${isVictory ? 'victory-text' : 'defeat-text'}`}
          style={{
            color: isVictory ? 'var(--text-gold)' : 'var(--crimson-glow)',
            textShadow: isVictory
              ? '0 0 20px rgba(212, 168, 48, 0.5)'
              : '0 0 20px rgba(204, 34, 34, 0.5)',
          }}
        >
          {isVictory ? 'VICTORY' : 'DEFEATED'}
        </h2>

        {/* Subtext */}
        <p
          className="text-base mb-8"
          style={{ color: 'var(--text-secondary)' }}
        >
          {isVictory
            ? 'The monster falls before your might!'
            : 'Your journey ends here...'}
        </p>

        {/* Rewards (victory only) */}
        {isVictory && (
          <div className="flex justify-center gap-6 mb-8">
            <div
              className="px-4 py-2 rounded-md"
              style={{ background: 'rgba(212, 168, 48, 0.1)', border: '1px solid rgba(212, 168, 48, 0.3)' }}
            >
              <span className="font-cinzel text-sm font-semibold" style={{ color: 'var(--text-gold)' }}>
                +150 EXP
              </span>
            </div>
            <div
              className="px-4 py-2 rounded-md"
              style={{ background: 'rgba(212, 168, 48, 0.1)', border: '1px solid rgba(212, 168, 48, 0.3)' }}
            >
              <span className="font-cinzel text-sm font-semibold" style={{ color: 'var(--text-gold)' }}>
                +50 Gold
              </span>
            </div>
          </div>
        )}

        {/* Continue Button */}
        <button
          onClick={onContinue}
          className="inline-flex items-center gap-2 px-8 py-3 rounded-md transition-all duration-200 hover:scale-105"
          style={{
            background: 'rgba(26, 26, 37, 0.9)',
            border: `1px solid ${isVictory ? 'rgba(212, 168, 48, 0.4)' : 'rgba(204, 34, 34, 0.4)'}`,
            boxShadow: isVictory
              ? '0 0 16px rgba(212, 168, 48, 0.2)'
              : '0 0 16px rgba(204, 34, 34, 0.2)',
          }}
          onMouseEnter={(e) => {
            e.currentTarget.style.borderColor = isVictory
              ? 'rgba(212, 168, 48, 0.6)'
              : 'rgba(204, 34, 34, 0.6)';
          }}
          onMouseLeave={(e) => {
            e.currentTarget.style.borderColor = isVictory
              ? 'rgba(212, 168, 48, 0.4)'
              : 'rgba(204, 34, 34, 0.4)';
          }}
        >
          <RotateCcw className="w-5 h-5" style={{ color: isVictory ? 'var(--text-gold)' : 'var(--crimson-glow)' }} />
          <span
            className="font-cinzel text-sm font-semibold uppercase tracking-wider"
            style={{ color: 'var(--text-primary)' }}
          >
            {isVictory ? 'Next Battle' : 'Rise Again'}
          </span>
        </button>
      </div>
    </div>
  );
}
