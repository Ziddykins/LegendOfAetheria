export interface EntityStats {
  hp: number;
  maxHP: number;
  mp: number;
  maxMP: number;
  ep: number;
  maxEP: number;
  str: number;
  def: number;
}

export interface Entity {
  name: string;
  level: number;
  stats: EntityStats;
}

export type CombatState =
  | 'idle'
  | 'player_turn'
  | 'processing'
  | 'enemy_turn'
  | 'animating'
  | 'resolved';

export type BattleResult = 'ongoing' | 'victory' | 'defeat';

export type ActionType = 'attack' | 'burn' | 'frost' | 'heal';

export interface CombatAction {
  type: ActionType;
  subtype?: string;
}

export interface CombatLogEntry {
  id: string;
  text: string;
  style: 'player' | 'enemy' | 'system' | 'victory' | 'defeat';
  timestamp: number;
}

export interface CombatResult {
  logEntries: CombatLogEntry[];
  playerStats: EntityStats;
  monsterStats: EntityStats;
  result: BattleResult;
  damageDealt?: number;
  damageTaken?: number;
  isCritical?: boolean;
  isMiss?: boolean;
  isBlock?: boolean;
  actionType: ActionType;
}

export interface HUDData {
  player: EntityStats;
  monster: EntityStats;
}

export interface BattleConfig {
  player: Entity;
  monster: Entity;
  csrfToken: string;
  apiEndpoint: string;
  state?: 'need-hunt' | 'combat';
  location?: {
    x: number;
    y: number;
  };
  onBattleEnd?: (result: BattleResult) => void;
  onNewBattle?: () => void;
}

export interface DamageNumberData {
  id: string;
  value: number;
  target: 'player' | 'monster';
  type: 'damage' | 'heal';
  isCritical?: boolean;
}

export type TargetType = 'player' | 'monster' | 'zone_monster' | 'global_monster' | 'other_player';

export interface SpellEffectData {
  id: string;
  type: 'fire' | 'ice' | 'heal';
  target: TargetType;
}
