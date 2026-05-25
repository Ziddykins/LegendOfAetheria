import { useCallback } from 'react';
import type {
  EntityStats,
  CombatAction,
  CombatResult,
  CombatLogEntry,
  BattleResult,
} from '@/types/battle';

const VERBS = [
  'attacks', 'pummels', 'strikes', 'assaults', 'bludgeons', 'ambushes',
  'beats', 'besieges', 'blasts', 'bombards', 'charges', 'harms',
  'hits', 'hurts', 'infiltrates', 'invades', 'raids', 'stabs', 'storms',
];

const ADVERBS = [
  'clumsily', 'lazily', 'spastically', 'carefully', 'precisely',
];

function roll(min: number, max: number): number {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function randomFloat(min: number, max: number, decimals: number): number {
  const val = Math.random() * (max - min) + min;
  return Number(val.toFixed(decimals));
}

function getVerb(): string {
  return VERBS[Math.floor(Math.random() * VERBS.length)];
}

function getAdverb(): string {
  return ADVERBS[Math.floor(Math.random() * ADVERBS.length)];
}

function generateId(): string {
  return Math.random().toString(36).substr(2, 9);
}

interface UseCombatEngineProps {
  playerName: string;
  monsterName: string;
}

export function useCombatEngine({ playerName, monsterName }: UseCombatEngineProps) {
  const executeCombat = useCallback(
    (
      action: CombatAction,
      currentPlayerStats: EntityStats,
      currentMonsterStats: EntityStats
    ): CombatResult => {
      const logEntries: CombatLogEntry[] = [];
      let playerStats = { ...currentPlayerStats };
      let monsterStats = { ...currentMonsterStats };
      let damageDealt = 0;
      let damageTaken = 0;
      let isCritical = false;
      let isMiss = false;
      let isBlock = false;
      let battleResult: BattleResult = 'ongoing';

      // Determine turn (coin flip)
      const isPlayerTurn = roll(1, 100) > 50;

      // Turn header
      logEntries.push({
        id: generateId(),
        text: `--- ${isPlayerTurn ? playerName : monsterName}'s Turn ---`,
        style: 'system',
        timestamp: Date.now(),
      });

      if (isPlayerTurn) {
        // Player attacks monster
        const attackRoll = roll(0, 100);
        const baseAttack = roll(1, playerStats.str);
        const attack = attackRoll === 100 ? baseAttack * 2 : baseAttack;
        const defense = roll(0, Math.floor(monsterStats.def * 0.8));
        let damage = Math.max(0, attack - defense);

        if (attackRoll === 100) {
          // Critical hit
          isCritical = true;
          const multiplier = randomFloat(1.5, 2.0, 2);
          damage = Math.floor(damage * multiplier);
          monsterStats.hp = Math.max(0, monsterStats.hp - damage);
          damageDealt = damage;
          logEntries.push({
            id: generateId(),
            text: `CRITICAL HIT! ${playerName} ${getVerb()} ${monsterName} for ${damage} damage! (${monsterStats.hp} HP left)`,
            style: 'system',
            timestamp: Date.now(),
          });
        } else if (attackRoll === 0) {
          // Miss
          isMiss = true;
          logEntries.push({
            id: generateId(),
            text: `${playerName} ${getAdverb()} ${getVerb()} ${monsterName} but misses!`,
            style: 'player',
            timestamp: Date.now(),
          });

          // Counter attack 30% chance
          if (roll(1, 100) > 70) {
            const counterDmg = roll(1, Math.floor(monsterStats.str * 0.5));
            playerStats.hp = Math.max(0, playerStats.hp - counterDmg);
            damageTaken = counterDmg;
            logEntries.push({
              id: generateId(),
              text: `${monsterName} sees an opening and counters for ${counterDmg} damage!`,
              style: 'enemy',
              timestamp: Date.now(),
            });
          }
        } else if (damage <= 0) {
          // Blocked
          isBlock = true;
          const parryChance = roll(1, 100);
          if (parryChance > 70) {
            const parryDmg = roll(1, Math.floor(monsterStats.str * 0.25));
            playerStats.hp = Math.max(0, playerStats.hp - parryDmg);
            damageTaken = parryDmg;
            logEntries.push({
              id: generateId(),
              text: `${monsterName} parries ${playerName}'s attack and deals ${parryDmg} damage!`,
              style: 'enemy',
              timestamp: Date.now(),
            });
          } else {
            monsterStats.hp = Math.max(0, monsterStats.hp - 1);
            damageDealt = 1;
            logEntries.push({
              id: generateId(),
              text: `${playerName} ${getVerb()} ${monsterName} but ${monsterName} blocks most of it! (1 damage)`,
              style: 'player',
              timestamp: Date.now(),
            });
          }
        } else {
          // Regular hit
          monsterStats.hp = Math.max(0, monsterStats.hp - damage);
          damageDealt = damage;
          logEntries.push({
            id: generateId(),
            text: `${playerName} ${getVerb()} ${monsterName} for ${damage} damage! (${monsterStats.hp} HP left)`,
            style: 'player',
            timestamp: Date.now(),
          });
        }

        // Spend EP
        playerStats.ep = Math.max(0, playerStats.ep - 1);
      } else {
        // Monster attacks player
        const attackRoll = roll(0, 100);
        const baseAttack = roll(1, monsterStats.str);
        const attack = attackRoll === 100 ? baseAttack * 2 : baseAttack;
        const defense = roll(0, Math.floor(playerStats.def * 0.8));
        let damage = Math.max(0, attack - defense);

        if (attackRoll === 100) {
          isCritical = true;
          const multiplier = randomFloat(1.5, 2.0, 2);
          damage = Math.floor(damage * multiplier);
          playerStats.hp = Math.max(0, playerStats.hp - damage);
          damageTaken = damage;
          logEntries.push({
            id: generateId(),
            text: `CRITICAL HIT! ${monsterName} ${getVerb()} ${playerName} for ${damage} damage! (${playerStats.hp} HP left)`,
            style: 'system',
            timestamp: Date.now(),
          });
        } else if (attackRoll === 0) {
          isMiss = true;
          logEntries.push({
            id: generateId(),
            text: `${monsterName} ${getAdverb()} ${getVerb()} ${playerName} but misses!`,
            style: 'enemy',
            timestamp: Date.now(),
          });

          if (roll(1, 100) > 70) {
            const counterDmg = roll(1, Math.floor(playerStats.str * 0.5));
            monsterStats.hp = Math.max(0, monsterStats.hp - counterDmg);
            damageDealt = counterDmg;
            logEntries.push({
              id: generateId(),
              text: `${playerName} sees an opening and counters for ${counterDmg} damage!`,
              style: 'player',
              timestamp: Date.now(),
            });
          }
        } else if (damage <= 0) {
          isBlock = true;
          const parryChance = roll(1, 100);
          if (parryChance > 70) {
            const parryDmg = roll(1, Math.floor(playerStats.str * 0.25));
            monsterStats.hp = Math.max(0, monsterStats.hp - parryDmg);
            damageDealt = parryDmg;
            logEntries.push({
              id: generateId(),
              text: `${playerName} parries ${monsterName}'s attack and deals ${parryDmg} damage!`,
              style: 'player',
              timestamp: Date.now(),
            });
          } else {
            playerStats.hp = Math.max(0, playerStats.hp - 1);
            damageTaken = 1;
            logEntries.push({
              id: generateId(),
              text: `${monsterName} ${getVerb()} ${playerName} but ${playerName} blocks most of it! (1 damage)`,
              style: 'enemy',
              timestamp: Date.now(),
            });
          }
        } else {
          playerStats.hp = Math.max(0, playerStats.hp - damage);
          damageTaken = damage;
          logEntries.push({
            id: generateId(),
            text: `${monsterName} ${getVerb()} ${playerName} for ${damage} damage! (${playerStats.hp} HP left)`,
            style: 'enemy',
            timestamp: Date.now(),
          });
        }
      }

      // Check battle result
      if (monsterStats.hp <= 0) {
        battleResult = 'victory';
        const expGained = roll(50, 200);
        const goldGained = roll(20, 100);
        logEntries.push({
          id: generateId(),
          text: `${monsterName} has been defeated!`,
          style: 'victory',
          timestamp: Date.now(),
        });
        logEntries.push({
          id: generateId(),
          text: `Victory! You gained ${expGained} experience and ${goldGained} gold!`,
          style: 'victory',
          timestamp: Date.now(),
        });
      } else if (playerStats.hp <= 0) {
        battleResult = 'defeat';
        logEntries.push({
          id: generateId(),
          text: `${playerName} has been defeated!`,
          style: 'defeat',
          timestamp: Date.now(),
        });
        logEntries.push({
          id: generateId(),
          text: 'Your journey ends here...',
          style: 'defeat',
          timestamp: Date.now(),
        });
      }

      return {
        logEntries,
        playerStats,
        monsterStats,
        result: battleResult,
        damageDealt,
        damageTaken,
        isCritical,
        isMiss,
        isBlock,
        actionType: action.type,
      };
    },
    [playerName, monsterName]
  );

  return { executeCombat };
}
