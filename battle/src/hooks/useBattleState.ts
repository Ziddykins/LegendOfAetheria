import { useReducer, useCallback } from 'react';
import type {
  CombatState,
  BattleResult,
  CombatAction,
  CombatLogEntry,
  CombatResult,
  EntityStats,
  DamageNumberData,
  SpellEffectData,
} from '@/types/battle';

interface BattleState {
  combatState: CombatState;
  battleResult: BattleResult;
  turn: 'player' | 'enemy';
  logEntries: CombatLogEntry[];
  playerStats: EntityStats;
  monsterStats: EntityStats;
  selectedAction: CombatAction | null;
  lastResult: CombatResult | null;
  damageNumbers: DamageNumberData[];
  spellEffects: SpellEffectData[];
  isShaking: boolean;
  playerFlash: 'damage' | 'heal' | null;
  monsterFlash: 'damage' | 'heal' | null;
  comboCount: number;
}

type BattleAction =
  | { type: 'INIT'; playerStats: EntityStats; monsterStats: EntityStats }
  | { type: 'SELECT_ACTION'; action: CombatAction }
  | { type: 'START_PROCESSING' }
  | { type: 'SHOW_RESULT'; result: CombatResult }
  | { type: 'ADD_LOG_ENTRY'; entry: CombatLogEntry }
  | { type: 'ADD_DAMAGE_NUMBER'; data: DamageNumberData }
  | { type: 'REMOVE_DAMAGE_NUMBER'; id: string }
  | { type: 'ADD_SPELL_EFFECT'; data: SpellEffectData }
  | { type: 'REMOVE_SPELL_EFFECT'; id: string }
  | { type: 'SET_SHAKING'; value: boolean }
  | { type: 'SET_PLAYER_FLASH'; flash: 'damage' | 'heal' | null }
  | { type: 'SET_MONSTER_FLASH'; flash: 'damage' | 'heal' | null }
  | { type: 'SET_TURN'; turn: 'player' | 'enemy' }
  | { type: 'ANIMATIONS_COMPLETE' }
  | { type: 'START_NEW_BATTLE'; playerStats: EntityStats; monsterStats: EntityStats }
  | { type: 'UPDATE_STATS'; playerStats: EntityStats; monsterStats: EntityStats };

const initialState: BattleState = {
  combatState: 'idle',
  battleResult: 'ongoing',
  turn: 'player',
  logEntries: [],
  playerStats: { hp: 0, maxHP: 0, mp: 0, maxMP: 0, ep: 0, maxEP: 0, str: 0, def: 0 },
  monsterStats: { hp: 0, maxHP: 0, mp: 0, maxMP: 0, ep: 0, maxEP: 0, str: 0, def: 0 },
  selectedAction: null,
  lastResult: null,
  damageNumbers: [],
  spellEffects: [],
  isShaking: false,
  playerFlash: null,
  monsterFlash: null,
  comboCount: 0,
};

function battleReducer(state: BattleState, action: BattleAction): BattleState {
  switch (action.type) {
    case 'INIT':
      return {
        ...initialState,
        playerStats: action.playerStats,
        monsterStats: action.monsterStats,
        combatState: 'player_turn',
        turn: 'player',
      };

    case 'SELECT_ACTION':
      if (state.combatState !== 'player_turn') return state;
      return { ...state, selectedAction: action.action };

    case 'START_PROCESSING':
      if (state.combatState !== 'player_turn') return state;
      return { ...state, combatState: 'processing' };

    case 'SHOW_RESULT': {
      const newEntries = [...action.result.logEntries, ...state.logEntries];
      return {
        ...state,
        combatState: 'animating',
        battleResult: action.result.result,
        lastResult: action.result,
        logEntries: newEntries,
        playerStats: action.result.playerStats,
        monsterStats: action.result.monsterStats,
      };
    }

    case 'ADD_LOG_ENTRY':
      return { ...state, logEntries: [action.entry, ...state.logEntries] };

    case 'ADD_DAMAGE_NUMBER':
      return { ...state, damageNumbers: [...state.damageNumbers, action.data] };

    case 'REMOVE_DAMAGE_NUMBER':
      return {
        ...state,
        damageNumbers: state.damageNumbers.filter((d) => d.id !== action.id),
      };

    case 'ADD_SPELL_EFFECT':
      return { ...state, spellEffects: [...state.spellEffects, action.data] };

    case 'REMOVE_SPELL_EFFECT':
      return {
        ...state,
        spellEffects: state.spellEffects.filter((e) => e.id !== action.id),
      };

    case 'SET_SHAKING':
      return { ...state, isShaking: action.value };

    case 'SET_PLAYER_FLASH':
      return { ...state, playerFlash: action.flash };

    case 'SET_MONSTER_FLASH':
      return { ...state, monsterFlash: action.flash };

    case 'SET_TURN':
      return { ...state, turn: action.turn };

    case 'ANIMATIONS_COMPLETE': {
      if (state.battleResult !== 'ongoing') {
        return { ...state, combatState: 'resolved' };
      }
      return { ...state, combatState: 'player_turn', turn: 'player' };
    }

    case 'START_NEW_BATTLE':
      return {
        ...initialState,
        playerStats: action.playerStats,
        monsterStats: action.monsterStats,
        combatState: 'player_turn',
        turn: 'player',
      };

    case 'UPDATE_STATS':
      return {
        ...state,
        playerStats: action.playerStats,
        monsterStats: action.monsterStats,
      };

    default:
      return state;
  }
}

export function useBattleState() {
  const [state, dispatch] = useReducer(battleReducer, initialState);

  const initBattle = useCallback(
    (playerStats: EntityStats, monsterStats: EntityStats) => {
      dispatch({ type: 'INIT', playerStats, monsterStats });
    },
    []
  );

  const selectAction = useCallback((action: CombatAction) => {
    dispatch({ type: 'SELECT_ACTION', action });
  }, []);

  const startProcessing = useCallback(() => {
    dispatch({ type: 'START_PROCESSING' });
  }, []);

  const showResult = useCallback((result: CombatResult) => {
    dispatch({ type: 'SHOW_RESULT', result });
  }, []);

  const addLogEntry = useCallback((entry: CombatLogEntry) => {
    dispatch({ type: 'ADD_LOG_ENTRY', entry });
  }, []);

  const addDamageNumber = useCallback((data: DamageNumberData) => {
    dispatch({ type: 'ADD_DAMAGE_NUMBER', data });
  }, []);

  const removeDamageNumber = useCallback((id: string) => {
    dispatch({ type: 'REMOVE_DAMAGE_NUMBER', id });
  }, []);

  const addSpellEffect = useCallback((data: SpellEffectData) => {
    dispatch({ type: 'ADD_SPELL_EFFECT', data });
  }, []);

  const removeSpellEffect = useCallback((id: string) => {
    dispatch({ type: 'REMOVE_SPELL_EFFECT', id });
  }, []);

  const setShaking = useCallback((value: boolean) => {
    dispatch({ type: 'SET_SHAKING', value });
  }, []);

  const setPlayerFlash = useCallback((flash: 'damage' | 'heal' | null) => {
    dispatch({ type: 'SET_PLAYER_FLASH', flash });
  }, []);

  const setMonsterFlash = useCallback((flash: 'damage' | 'heal' | null) => {
    dispatch({ type: 'SET_MONSTER_FLASH', flash });
  }, []);

  const setTurn = useCallback((turn: 'player' | 'enemy') => {
    dispatch({ type: 'SET_TURN', turn });
  }, []);

  const animationsComplete = useCallback(() => {
    dispatch({ type: 'ANIMATIONS_COMPLETE' });
  }, []);

  const startNewBattle = useCallback(
    (playerStats: EntityStats, monsterStats: EntityStats) => {
      dispatch({ type: 'START_NEW_BATTLE', playerStats, monsterStats });
    },
    []
  );

  const updateStats = useCallback(
    (playerStats: EntityStats, monsterStats: EntityStats) => {
      dispatch({ type: 'UPDATE_STATS', playerStats, monsterStats });
    },
    []
  );

  return {
    state,
    initBattle,
    selectAction,
    startProcessing,
    showResult,
    addLogEntry,
    addDamageNumber,
    removeDamageNumber,
    addSpellEffect,
    removeSpellEffect,
    setShaking,
    setPlayerFlash,
    setMonsterFlash,
    setTurn,
    animationsComplete,
    startNewBattle,
    updateStats,
  };
}
