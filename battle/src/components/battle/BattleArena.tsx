import { useEffect, useState, useRef, useCallback } from 'react';
import { ArrowUp, ArrowDown, ArrowLeft, ArrowRight, Compass } from 'lucide-react';
import { EntityPanel } from './EntityPanel';
import { ActionBar } from './ActionBar';
import { BattleLog } from './BattleLog';
import { TurnIndicator } from './TurnIndicator';
import { DamageNumber } from './DamageNumber';
import { CombatEffects } from './CombatEffects';
import { VictoryOverlay } from './VictoryOverlay';
import { EmberParticles } from './EmberParticles';
import { useBattleState } from '@/hooks/useBattleState';
import { useCombatAnimation } from '@/hooks/useCombatAnimation';
import { useCombatEngine } from '@/hooks/useCombatEngine';
import type { BattleConfig, CombatAction } from '@/types/battle';

interface BattleArenaProps {
  config: BattleConfig;
}

export function BattleArena({ config }: BattleArenaProps) {
  const {
    state,
    initBattle,
    startProcessing,
    showResult,
    addDamageNumber,
    removeDamageNumber,
    addSpellEffect,
    removeSpellEffect,
    setShaking,
    setPlayerFlash,
    setMonsterFlash,
    animationsComplete,
    startNewBattle,
  } = useBattleState();

  const { executeCombat } = useCombatEngine({
    playerName: config.player.name,
    monsterName: config.monster.name
  });

  const { playCombatAnimation, playEntryAnimation, killTimeline } =
    useCombatAnimation({
      addDamageNumber,
      removeDamageNumber,
      addSpellEffect,
      removeSpellEffect,
      setShaking,
      setPlayerFlash,
      setMonsterFlash,
      animationsComplete,
    });

  const containerRef = useRef<HTMLDivElement>(null);
  const initialized = useRef(false);
  const isExplorationMode = config.state === 'need-hunt';
  const [location, setLocation] = useState(() => config.location ?? { x: 0, y: 0 });

  const clamp = useCallback((value: number, min: number, max: number) => {
    return Math.min(max, Math.max(min, value));
  }, []);

  const movePlayer = useCallback(
    (dx: number, dy: number) => {
      setLocation((prev) => ({
        x: clamp(prev.x + dx, -100, 100),
        y: clamp(prev.y + dy, -100, 100),
      }));
    },
    [clamp]
  );

  useEffect(() => {
    if (config.state === 'need-hunt' && config.location) {
      setLocation(config.location);
    }
  }, [config.state, config.location]);

  // Initialize battle on mount when not in exploration mode
  useEffect(() => {
    if (!initialized.current && !isExplorationMode) {
      initialized.current = true;
      initBattle(config.player.stats, config.monster.stats);
    }
  }, [config.player.stats, config.monster.stats, initBattle, isExplorationMode]);

  // Entry animation
  useEffect(() => {
    if (state.combatState === 'player_turn' && containerRef.current) {
      playEntryAnimation(containerRef);
    }
  }, [state.combatState, playEntryAnimation]);

  // Handle combat result with animation
  useEffect(() => {
    if (state.combatState === 'animating' && state.lastResult) {
      playCombatAnimation(state.lastResult);
    }
  }, [state.combatState, state.lastResult, playCombatAnimation]);

  // Cleanup on unmount
  useEffect(() => {
    return () => {
      killTimeline();
    };
  }, [killTimeline]);

  const handleAction = useCallback(
    (action: CombatAction) => {
      if (state.combatState !== 'player_turn') return;

      startProcessing();

      // Simulate server processing delay
      setTimeout(() => {
        const result = executeCombat(
          action,
          state.playerStats,
          state.monsterStats
        );
        showResult(result);
      }, 600);
    },
    [state.combatState, state.playerStats, state.monsterStats, startProcessing, executeCombat, showResult]
  );

  const handleNewBattle = useCallback(() => {
    // Reset stats for new battle
    const freshPlayerStats = { ...config.player.stats, hp: config.player.stats.maxHP, ep: config.player.stats.maxEP };
    const freshMonsterStats = { ...config.monster.stats, hp: config.monster.stats.maxHP };
    startNewBattle(freshPlayerStats, freshMonsterStats);
  }, [config, startNewBattle]);

  const isPlayerTurn =
    state.combatState === 'player_turn' || state.combatState === 'processing';
  const hasEP = state.playerStats.ep > 0;
  const isActionEnabled = state.combatState === 'player_turn' && hasEP;

  if (isExplorationMode) {
    return (
      <div
        ref={containerRef}
        className={`battle-arena-bg relative w-full max-w-3xl mx-auto rounded-lg overflow-hidden ${
          state.isShaking ? 'screen-shake' : ''
        }`}
        style={{
          minHeight: 500,
          border: '1px solid rgba(42, 42, 58, 0.6)',
          boxShadow: '0 8px 48px rgba(0,0,0,0.6)',
        }}
      >
        <EmberParticles />

        <div className="relative z-10 flex flex-col items-center justify-center gap-6 px-6 py-8" style={{ minHeight: 500 }}>
          <div className="text-center max-w-xl">
            <div
              className="font-cinzel text-xs uppercase tracking-[0.32em]"
              style={{ color: 'var(--text-muted)' }}
            >
              Exploration Mode
            </div>
            <h2
              className="font-cinzel text-3xl font-bold mt-4"
              style={{ color: 'var(--text-primary)' }}
            >
              Search the Open Plane
            </h2>
            <p
              className="mt-3 text-sm leading-6"
              style={{ color: 'var(--text-muted)' }}
            >
              No monsters are nearby. Use the compass to move your position across the plain and hunt for danger.
            </p>
          </div>

          <div
            className="grid grid-cols-3 items-center gap-4 rounded-3xl border border-[rgba(255,255,255,0.08)] bg-[rgba(12,12,20,0.92)] p-6 shadow-[0_0_60px_rgba(0,0,0,0.35)]"
            style={{ width: '100%', maxWidth: 420 }}
          >
            <div />
            <button
              type="button"
              onClick={() => movePlayer(0, -10)}
              className="flex h-14 w-14 items-center justify-center rounded-full border border-[rgba(255,255,255,0.08)] bg-[rgba(255,255,255,0.04)] transition hover:border-[rgba(139,26,26,0.35)] hover:bg-[rgba(255,255,255,0.08)]"
            >
              <ArrowUp className="w-6 h-6 text-[var(--text-primary)]" />
            </button>
            <div />

            <button
              type="button"
              onClick={() => movePlayer(-10, 0)}
              className="flex h-14 w-14 items-center justify-center rounded-full border border-[rgba(255,255,255,0.08)] bg-[rgba(255,255,255,0.04)] transition hover:border-[rgba(139,26,26,0.35)] hover:bg-[rgba(255,255,255,0.08)]"
            >
              <ArrowLeft className="w-6 h-6 text-[var(--text-primary)]" />
            </button>

            <div className="flex flex-col items-center justify-center gap-2 rounded-3xl border border-[rgba(255,255,255,0.08)] bg-[rgba(15,15,25,0.96)] p-4">
              <Compass className="w-10 h-10 text-[var(--text-primary)]" />
              <div className="text-center">
                <div className="text-[0.72rem] uppercase tracking-[0.3em]" style={{ color: 'var(--text-muted)' }}>
                  Coordinates
                </div>
                <div className="font-cinzel text-2xl font-bold" style={{ color: 'var(--text-primary)' }}>
                  {location.x}, {location.y}
                </div>
              </div>
            </div>

            <button
              type="button"
              onClick={() => movePlayer(10, 0)}
              className="flex h-14 w-14 items-center justify-center rounded-full border border-[rgba(255,255,255,0.08)] bg-[rgba(255,255,255,0.04)] transition hover:border-[rgba(139,26,26,0.35)] hover:bg-[rgba(255,255,255,0.08)]"
            >
              <ArrowRight className="w-6 h-6 text-[var(--text-primary)]" />
            </button>

            <div />
            <button
              type="button"
              onClick={() => movePlayer(0, 10)}
              className="flex h-14 w-14 items-center justify-center rounded-full border border-[rgba(255,255,255,0.08)] bg-[rgba(255,255,255,0.04)] transition hover:border-[rgba(139,26,26,0.35)] hover:bg-[rgba(255,255,255,0.08)]"
            >
              <ArrowDown className="w-6 h-6 text-[var(--text-primary)]" />
            </button>
            <div />
          </div>

          <div className="flex flex-col items-center gap-1 text-sm" style={{ color: 'var(--text-muted)' }}>
            <span>Plane bounds: x, y ∈ [-100, 100]</span>
            <span>Step size: 10 units</span>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div
      ref={containerRef}
      className={`battle-arena-bg relative w-full max-w-3xl mx-auto rounded-lg overflow-hidden ${
        state.isShaking ? 'screen-shake' : ''
      }`}
      style={{
        minHeight: 500,
        border: '1px solid rgba(42, 42, 58, 0.6)',
        boxShadow: '0 8px 48px rgba(0,0,0,0.6)',
      }}
    >
      {/* Background particles */}
      <EmberParticles />

      if (config.state === 'need-hunt') {

      }

      {/* Content */}
      <div className="relative z-10 flex flex-col" style={{ minHeight: 500 }}>
        {/* Turn Indicator */}
        <TurnIndicator turn={state.turn} combatState={state.combatState} />

        {/* Entity Row */}
        <div className="flex items-stretch gap-3 px-4 py-4">
          {/* Player Panel */}
          <div className="flex-1 relative">
            <EntityPanel
              name={config.player.name}
              level={config.player.level}
              stats={state.playerStats}
              isPlayer={true}
              isActive={isPlayerTurn}
              flashType={state.playerFlash}
            />
            {/* Damage numbers for player */}
            {state.damageNumbers
              .filter((d) => d.target === 'player')
              .map((d) => (
                <DamageNumber
                  key={d.id}
                  data={d}
                  onComplete={removeDamageNumber}
                />
              ))}
          </div>

          {/* VS Divider */}
          <div className="vs-divider flex flex-col items-center justify-center" style={{ width: 50 }}>
            <div
              className="w-px flex-1"
              style={{ background: 'linear-gradient(180deg, transparent, rgba(42,42,58,0.6), transparent)' }}
            />
            <span
              className="font-cinzel text-lg font-bold my-2"
              style={{
                color: 'var(--text-muted)',
                textShadow: '0 0 8px rgba(139, 26, 26, 0.3)',
                letterSpacing: '0.08em',
              }}
            >
              VS
            </span>
            <div
              className="w-px flex-1"
              style={{ background: 'linear-gradient(180deg, transparent, rgba(42,42,58,0.6), transparent)' }}
            />
          </div>

          {/* Monster Panel */}
          <div className="flex-1 relative">
            <EntityPanel
              name={config.monster.name}
              level={config.monster.level}
              stats={state.monsterStats}
              isPlayer={false}
              isActive={!isPlayerTurn}
              flashType={state.monsterFlash}
            />
            {/* Damage numbers for monster */}
            {state.damageNumbers
              .filter((d) => d.target === 'monster')
              .map((d) => (
                <DamageNumber
                  key={d.id}
                  data={d}
                  onComplete={removeDamageNumber}
                />
              ))}
          </div>
        </div>

        {/* Spell effects overlay */}
        <CombatEffects
          damageNumbers={[]}
          spellEffects={state.spellEffects}
          onDamageComplete={removeDamageNumber}
          onSpellComplete={removeSpellEffect}
        />

        {/* Action Bar */}
        <div className="px-4">
          <ActionBar
            isEnabled={isActionEnabled}
            isProcessing={state.combatState === 'processing'}
            hasEP={hasEP}
            playerHP={state.playerStats.hp}
            playerMaxHP={state.playerStats.maxHP}
            onAction={handleAction}
          />
        </div>

        {/* Battle Log */}
        <div className="px-4 py-3 flex-1">
          <BattleLog entries={state.logEntries} />
        </div>

        {/* EP Warning */}
        {!hasEP && state.combatState !== 'resolved' && (
          <div
            className="text-center py-2 font-cinzel text-xs font-semibold uppercase tracking-wider"
            style={{ color: 'var(--warning-orange)', background: 'rgba(255, 136, 0, 0.05)' }}
          >
            No Energy Points! Cannot take actions.
          </div>
        )}
      </div>

      {/* Victory/Defeat Overlay */}
      {state.combatState === 'resolved' && (
        <VictoryOverlay result={state.battleResult} onContinue={handleNewBattle} />
      )}
    </div>
  );
}
