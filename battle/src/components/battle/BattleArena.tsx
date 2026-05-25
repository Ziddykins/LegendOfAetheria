import { useEffect, useRef, useCallback } from 'react';
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
    monsterName: config.monster.name,
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

  // Initialize battle on mount
  useEffect(() => {
    if (!initialized.current) {
      initialized.current = true;
      initBattle(config.player.stats, config.monster.stats);
    }
  }, [config, initBattle]);

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
