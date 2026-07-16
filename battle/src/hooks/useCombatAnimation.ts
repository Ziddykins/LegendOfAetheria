import { useRef, useCallback } from 'react';
import gsap from 'gsap';
import type {
  CombatResult,
  DamageNumberData,
  SpellEffectData,
} from '@/types/battle';

interface AnimationCallbacks {
  addDamageNumber: (data: DamageNumberData) => void;
  removeDamageNumber: (id: string) => void;
  addSpellEffect: (data: SpellEffectData) => void;
  removeSpellEffect: (id: string) => void;
  setShaking: (value: boolean) => void;
  setPlayerFlash: (flash: 'damage' | 'heal' | null) => void;
  setMonsterFlash: (flash: 'damage' | 'heal' | null) => void;
  animationsComplete: () => void;
}

export function useCombatAnimation(callbacks: AnimationCallbacks) {
  const timelineRef = useRef<gsap.core.Timeline | null>(null);

  const playCombatAnimation = useCallback(
    (result: CombatResult) => {
      // Kill any existing timeline
      if (timelineRef.current) {
        timelineRef.current.kill();
      }

      const tl = gsap.timeline({
        onComplete: () => {
          callbacks.animationsComplete();
        },
      });
      timelineRef.current = tl;

      const hasPlayerAction =
        result.actionType === 'attack' ||
        result.actionType === 'burn' ||
        result.actionType === 'frost';
      const hasHeal = result.actionType === 'heal';

      // Hit stop
      tl.to({}, { duration: 0.1 });

      // Spell/attack effects
      if (hasPlayerAction && (result.damageDealt ?? 0) > 0) {
        // Monster takes damage
        tl.call(() => {
          callbacks.setMonsterFlash('damage');
          const id = Math.random().toString(36).substr(2, 9);
          callbacks.addDamageNumber({
            id,
            value: result.damageDealt || 0,
            target: 'monster',
            type: 'damage',
            isCritical: result.isCritical,
          });
          setTimeout(() => callbacks.removeDamageNumber(id), 800);
        });
        tl.to({}, { duration: 0.2 });

        // Screen shake on critical or heavy damage
        if (result.isCritical || (result.damageDealt || 0) > 50) {
          tl.call(() => callbacks.setShaking(true));
          tl.to({}, { duration: 0.3 });
          tl.call(() => callbacks.setShaking(false));
        }

        tl.call(() => callbacks.setMonsterFlash(null));
      }

      if (hasHeal && (result.damageDealt ?? 0) > 0) {
        tl.call(() => {
          callbacks.setPlayerFlash('heal');
          const id = Math.random().toString(36).substr(2, 9);
          callbacks.addDamageNumber({
            id,
            value: result.damageDealt || 0,
            target: 'player',
            type: 'heal',
          });
          setTimeout(() => callbacks.removeDamageNumber(id), 800);
        });
        tl.to({}, { duration: 0.2 });
        tl.call(() => callbacks.setPlayerFlash(null));
      }

      // Spell visual effects
      if (result.actionType === 'burn') {
        tl.call(() => {
          const id = Math.random().toString(36).substr(2, 9);
          callbacks.addSpellEffect({ id, type: 'fire', target: 'monster' });
          setTimeout(() => callbacks.removeSpellEffect(id), 400);
        });
      }

      if (result.actionType === 'frost') {
        tl.call(() => {
          const id = Math.random().toString(36).substr(2, 9);
          callbacks.addSpellEffect({ id, type: 'ice', target: 'monster' });
          setTimeout(() => callbacks.removeSpellEffect(id), 400);
        });
      }

      if (result.actionType === 'heal') {
        tl.call(() => {
          const id = Math.random().toString(36).substr(2, 9);
          callbacks.addSpellEffect({ id, type: 'heal', target: 'player' });
          setTimeout(() => callbacks.removeSpellEffect(id), 400);
        });
      }

      // Enemy damage to player
      if ((result.damageTaken ?? 0) > 0) {
        tl.call(() => {
          callbacks.setPlayerFlash('damage');
          const id = Math.random().toString(36).substr(2, 9);
          callbacks.addDamageNumber({
            id,
            value: result.damageTaken || 0,
            target: 'player',
            type: 'damage',
          });
          setTimeout(() => callbacks.removeDamageNumber(id), 800);
        });
        tl.to({}, { duration: 0.2 });
        tl.call(() => callbacks.setPlayerFlash(null));
      }

      tl.to({}, { duration: 0.1 });
    },
    [callbacks]
  );

  const playEntryAnimation = useCallback(
    (containerRef: React.RefObject<HTMLDivElement | null>) => {
      if (!containerRef.current) return;
      const cards = containerRef.current.querySelectorAll('.entity-card');
      const vsDivider = containerRef.current.querySelectorAll('.vs-divider');

      gsap.set([...Array.from(cards), ...Array.from(vsDivider)], {
        opacity: 0,
        y: 20,
      });

      const tl = gsap.timeline();
      tl.to(cards[0], { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' });
      tl.to(
        vsDivider[0],
        { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' },
        '-=0.3'
      );
      if (cards[1]) {
        tl.to(
          cards[1],
          { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' },
          '-=0.3'
        );
      }
    },
    []
  );

  const playVictoryAnimation = useCallback(
    (overlayRef: React.RefObject<HTMLDivElement | null>) => {
      if (!overlayRef.current) return;
      const text = overlayRef.current.querySelector('.victory-text');
      if (text) {
        gsap.fromTo(
          text,
          { scale: 0.5, opacity: 0 },
          { scale: 1, opacity: 1, duration: 0.6, ease: 'back.out(1.7)' }
        );
      }
      gsap.fromTo(
        overlayRef.current,
        { opacity: 0 },
        { opacity: 1, duration: 0.4 }
      );
    },
    []
  );

  const playDefeatAnimation = useCallback(
    (overlayRef: React.RefObject<HTMLDivElement | null>) => {
      if (!overlayRef.current) return;
      const text = overlayRef.current.querySelector('.defeat-text');
      if (text) {
        gsap.fromTo(
          text,
          { scale: 0.5, opacity: 0 },
          { scale: 1, opacity: 1, duration: 0.6, ease: 'back.out(1.7)' }
        );
      }
      gsap.fromTo(
        overlayRef.current,
        { opacity: 0 },
        { opacity: 1, duration: 0.4 }
      );
    },
    []
  );

  const killTimeline = useCallback(() => {
    if (timelineRef.current) {
      timelineRef.current.kill();
    }
  }, []);

  return {
    playCombatAnimation,
    playEntryAnimation,
    playVictoryAnimation,
    playDefeatAnimation,
    killTimeline,
  };
}
