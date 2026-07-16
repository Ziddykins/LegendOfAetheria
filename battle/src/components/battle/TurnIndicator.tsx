import { useRef, useEffect } from 'react';
import gsap from 'gsap';

interface TurnIndicatorProps {
  turn: 'player' | 'enemy';
  combatState: string;
}

export function TurnIndicator({ turn, combatState }: TurnIndicatorProps) {
  const textRef = useRef<HTMLDivElement>(null);
  const prevTurnRef = useRef(turn);

  useEffect(() => {
    if (prevTurnRef.current !== turn && textRef.current) {
      gsap.fromTo(
        textRef.current,
        { scale: 1.3, opacity: 0 },
        { scale: 1, opacity: 1, duration: 0.4, ease: 'power2.out' }
      );
      prevTurnRef.current = turn;
    }
  }, [turn]);

  const isPlayerTurn = turn === 'player';
  const turnText = isPlayerTurn ? 'YOUR TURN' : 'ENEMY TURN';
  const turnColor = isPlayerTurn ? 'var(--text-gold)' : 'var(--crimson-glow)';
  const bgGradient = isPlayerTurn
    ? 'linear-gradient(90deg, transparent, rgba(212, 168, 48, 0.1), transparent)'
    : 'linear-gradient(90deg, transparent, rgba(204, 34, 34, 0.1), transparent)';

  const getStateText = () => {
    switch (combatState) {
      case 'processing':
        return 'Processing...';
      case 'animating':
        return '...';
      case 'resolved':
        return 'Battle Ended';
      default:
        return turnText;
    }
  };

  return (
    <div
      className="h-10 flex items-center justify-center"
      style={{ background: bgGradient }}
    >
      <div
        ref={textRef}
        className="font-cinzel text-sm font-semibold uppercase tracking-widest"
        style={{ color: turnColor, letterSpacing: '0.12em' }}
      >
        {getStateText()}
      </div>
    </div>
  );
}
