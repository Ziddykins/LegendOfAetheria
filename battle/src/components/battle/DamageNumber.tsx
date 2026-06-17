import { useRef, useEffect } from 'react';
import gsap from 'gsap';
import type { DamageNumberData } from '@/types/battle';

interface DamageNumberProps {
  data: DamageNumberData;
  onComplete: (id: string) => void;
}

export function DamageNumber({ data, onComplete }: DamageNumberProps) {
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!ref.current) return;

    const tl = gsap.timeline({
      onComplete: () => onComplete(data.id),
    });

    tl.fromTo(
      ref.current,
      { y: 0, opacity: 1, scale: 1.2 },
      { y: -40, opacity: 0, scale: 1, duration: 0.8, ease: 'power2.out' }
    );

    return () => {
      tl.kill();
    };
  }, [data.id, onComplete]);

  const isHeal = data.type === 'heal';
  const color = isHeal ? 'var(--spell-heal)' : 'var(--crimson-glow)';
  const prefix = isHeal ? '+' : '-';
  const shadowColor = isHeal
    ? 'rgba(68, 204, 102, 0.5)'
    : 'rgba(204, 34, 34, 0.5)';

  return (
    <div
      ref={ref}
      className="absolute pointer-events-none z-50"
      style={{
        left: '50%',
        top: '30%',
        transform: 'translateX(-50%)',
      }}
    >
      <div
        className="font-cinzel text-2xl font-black whitespace-nowrap"
        style={{
          color,
          textShadow: `0 0 10px ${shadowColor}`,
          fontSize: data.isCritical ? '32px' : '24px',
        }}
      >
        {prefix}{data.value}
        {data.isCritical && (
          <span className="block text-center text-xs uppercase tracking-wider mt-1" style={{ color: 'var(--text-gold)' }}>
            Critical!
          </span>
        )}
      </div>
    </div>
  );
}
