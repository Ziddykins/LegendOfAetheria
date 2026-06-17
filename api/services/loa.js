import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const SAVE_FILE = path.join(__dirname, '..', '..', 'loa.eng');

let Engine;
let WorldStore;
let buildCombatStack;
let createDialogueCore;
let traversalCore;
let statusCore;

async function loadAiRpgPackages() {
  if (Engine) return;
  const core = await import('@ai-rpg-engine/core');
  const modules = await import('@ai-rpg-engine/modules');

  Engine = core.Engine;
  WorldStore = core.WorldStore;
  buildCombatStack = modules.buildCombatStack;
  createDialogueCore = modules.createDialogueCore;
  traversalCore = modules.traversalCore;
  statusCore = modules.statusCore;
}

function getDialogueDefinitions() {
  return [{
    id: 'sage_intro',
    ownerId: 'question-sage',
    startNodeId: 'start',
    entryNodeId: 'start',
    speakers: ['Question Sage'],
    nodes: {
      start: {
        text: "Welcome. You come seeking quests. Little do you know, you've been on one the minute you walked through that door.",
        choices: [
          { text: '...huh?', nextNodeId: 'clueless', type: { color: 'bg-primary', icon: 'emoji-astonished-fill' } },
          { text: 'Shut it, old man! Out with the quests or die.', nextNodeId: 'rude', type: { color: 'bg-danger', icon: 'emoji-angry-fill' } },
        ],
      },
      clueless: {
        text: 'Oh, nothing. Here, drink this quest-enabling potion.',
        choices: [
          { text: 'You got it, sport-o!', nextNodeId: 'end_power', type: { color: 'bg-success', icon: 'emoji-sunglasses-fill' } },
          { text: "I ain't quaffin' a thing, later creep-o", nextNodeId: 'rude', type: { color: 'bg-warning', icon: 'emoji-neutral-fill' } },
        ],
      },
      rude: {
        text: 'You fool... You foolish FOOL! You know what? I don\'t even wanna give you the only potion in the world that enables quests. Go live your questless life without quests, there, No-Quests. I\'ll just be over here, on a quest, questin\' it up.',
        choices: [
          { text: 'Wait, the only one? Gimme it or die, old man!', nextNodeId: 'end_power', type: { color: 'bg-warning', icon: 'award-fill' } },
          { text: 'Well, can you sweeten the deal a little, maybe toss in another potion? That green one looks tasty. I\'d quaff that.', nextNodeId: 'bargain', type: { color: 'bg-success', icon: 'flask-florence-fill' } },
        ],
      },
      end_power: {
        text: 'Good, good. Now we play the waiting game...',
        effects: [{ type: 'addItem', targetId: 'hero', itemId: 1, itemType: 'consumables' }],
        end: true,
      },
      bargain: {
        text: 'Hrmph... Let me check the back room. Ah, yes, fine. Here.',
        effects: [
          { type: 'addItem', targetId: 'hero', itemId: 4, itemType: 'consumables' },
          { type: 'addItem', targetId: 'hero', itemId: 1, itemType: 'consumables' },
        ],
        end: true,
      },
    },
  }];
}

const ITEMS = {
  helmet: [
    {
      type: 'helmet',
      itemId: 1,
      name: 'Rustbound Helm',
      image: 'img/items/equipment/helmet/rustbound_helm.png',
      weight: 7,
      maxSockets: 1,
      rarity: null,
      expireTick: null,
      implicit: [{ target: 'def', range: [3, 7] }],
      affixPool: [
        { target: 'def', range: [2, 10] },
        { target: 'rsst', range: [2, 8] },
        { target: 'maxHP', range: [10, 40] },
        { target: 'hp', range: [5, 25] },
        { target: 'mdef', range: [2, 8] },
      ],
      description: 'A corroded helm that still holds firm.',
    },
  ],
  consumables: [
    {
      type: 'consumables',
      subtype: 'potion',
      itemId: 1,
      name: 'Quest Enabler',
      image: 'img/items/potions/quest_enabler.png',
      weight: 1,
      maxSockets: 0,
      rarity: 'LEGENDARY',
      expireTick: null,
      implicit: [],
      affixPool: [],
      description: 'The only potion in the world that unseals hidden paths and awakens dormant quests.',
      stackable: true,
    },
    {
      type: 'consumables',
      subtype: 'potion',
      itemId: 4,
      name: 'Small Potion of Resistance',
      image: 'img/items/potions/resistance.png',
      weight: 1,
      maxSockets: 0,
      rarity: 'ENCHANTED',
      expireTick: null,
      implicit: [{ target: 'rsst', range: [1, 5] }],
      affixPool: [],
      description: 'Permanently contributes your ability to resist things like catching on fire and being bitten by snakes.',
      stackable: true,
    },
  ],
};

function getItemById(type, id) {
  const category = ITEMS[type];
  return category?.find(item => item.itemId === id);
}

async function saveEngine(engine) {
  const engineStr = JSON.stringify({
    world: engine.store.serialize(),
    actionLog: engine.getActionLog(),
  });

  fs.writeFileSync(SAVE_FILE, engineStr, 'utf8');
  return engineStr;
}

async function loadEngine() {
  await loadAiRpgPackages();
  const contents = fs.readFileSync(SAVE_FILE, 'utf8').trim();
  const dialogueModule = createDialogueCore([]);
  const combatStack = buildCombatStack({
    statMapping: { attack: 'strength', precision: 'dexterity', resolve: 'intelligence' },
    playerId: 'hero',
  });

  let tmpEngine;

  if (contents && contents !== 'undefined' && contents !== 'null') {
    tmpEngine = new Engine({
      manifest: {
        id: 'loa',
        title: 'Legend of Aetheria',
        version: '1.0.0',
        engineVersion: '2.3.1',
        ruleset: loaRuleset.id,
        modules: [],
        contentPacks: [],
      },
      seed: Math.floor(Math.random() * 1_000_000),
      modules: [
        statusCore,
        traversalCore,
        ...combatStack.modules,
        dialogueModule,
      ],
    });

    const parsed = JSON.parse(contents);
    const restoredStore = WorldStore.deserialize(parsed.world, tmpEngine.store.events);
    tmpEngine.store = restoredStore;
    tmpEngine.actionLog = parsed.actionLog || [];
  }

  return tmpEngine;
}

async function createGameEngine() {
  await loadAiRpgPackages();

  const combatStack = buildCombatStack({
    statMapping: { attack: 'strength', precision: 'intelligence', resolve: 'defense' },
    playerId: 'hero',
  });

  const dialogueDefinitions = getDialogueDefinitions();
  const normalizedDialogueDefinitions = dialogueDefinitions.map(dialogue => ({
    ...dialogue,
    speakers: dialogue.speakers || [],
  }));

  const dialogueCoreRaw = createDialogueCore(normalizedDialogueDefinitions);
  const dialogueModules = Array.isArray(dialogueCoreRaw) ? dialogueCoreRaw : [dialogueCoreRaw];

  let engine = new Engine({
    manifest: {
      id: 'loa',
      title: 'Legend of Aetheria',
      version: '1.1.0',
      engineVersion: '1.1.0',
      ruleset: 'loa',
      modules: [],
      contentPacks: [],
    },
    seed: Math.floor(Math.random() * 1_000_000),
    modules: [
      statusCore,
      traversalCore,
      ...combatStack.modules,
      ...dialogueModules,
    ],
  });

  try {
    const loaded = await loadEngine();
    if (loaded) {
      engine = loaded;
      console.log('📂 Save loaded');
    } else {
      bootstrapWorld(engine);
    }
  } catch (err) {
    console.warn('⚠️ Failed to load save, creating new world: ' + err);
    bootstrapWorld(engine);
  }

  await saveEngine(engine);
  return engine;
}

function bootstrapWorld(engine) {
  engine.store.addZone({
    id: 'sanctuary',
    roomId: 'sanctuary',
    name: 'Sanctuary',
    tags: ['safe'],
    neighbors: ['forest'],
  });

  engine.store.addZone({
    id: 'forest',
    roomId: 'dark-forest',
    name: 'Dark Forest',
    tags: ['danger'],
    neighbors: ['sanctuary'],
  });

  engine.store.addEntity({
    id: 'hero',
    type: 'player',
    name: 'Hero',
    stats: { strength: 6, defense: 5, intelligence: 4 },
    resources: { hp: 25 },
    statuses: [],
    blueprintId: '',
    tags: [''],
  });

  engine.store.state.playerId = 'hero';
  engine.store.state.locationId = 'sanctuary';

  engine.store.addEntity({
    id: 'wolf',
    type: 'enemy',
    name: 'Wolf',
    stats: { strength: 4, defense: 6, intelligence: 2 },
    resources: { hp: 12 },
    statuses: [],
    zoneId: 'forest',
    blueprintId: 'melee',
    tags: [],
  });

  engine.store.addEntity({
    id: 'question-sage',
    type: 'npc',
    name: 'QUESTion Sage',
    tags: ['wise', 'old'],
    zoneId: 'sanctuary',
    blueprintId: '',
    resources: { hp: 10 },
    stats: { strength: 4, defense: 1, intelligence: 8 },
    statuses: [],
  });
}

const loaRuleset = {
  id: 'loa-rs',
  name: 'LoA Ruleset',
  version: '1.0.0',
  stats: [
    { id: 'strength', name: 'Strength', min: 1, default: 6 },
    { id: 'defense', name: 'Defense', min: 1, default: 5 },
    { id: 'intelligence', name: 'Intelligence', min: 1, default: 4 },
  ],
  resources: [
    { id: 'hp', name: 'Health', min: 0, default: 10 },
    { id: 'mp', name: 'Mana', min: 0, default: 10 },
    { id: 'en', name: 'Energy', min: 0, default: 10, regenRate: 2 },
  ],
  defaultModules: ['traversal-core', 'status-core', 'combat-core', 'inventory-core', 'dialogue-core'],
  formulas: [
    {
      id: 'melee',
      name: 'Melee',
      description: 'Base: attacker.strength, minimum 1',
      inputs: ['attacker.strength'],
      output: 'number',
    },
  ],
  verbs: [
    { id: 'move', name: 'Move', description: 'Navigate to adjacent node' },
    { id: 'inspect', name: 'Inspect', description: 'Scan current node or target' },
    { id: 'hack', name: 'Hack', tags: ['netrunning'], description: 'Attempt to breach a system' },
    { id: 'attack', name: 'Zap', tags: ['combat'], description: 'Hit with a stun baton or similar' },
    { id: 'guard', name: 'Guard', tags: ['combat', 'defensive'], description: 'Brace for incoming attacks, reducing damage taken' },
    { id: 'disengage', name: 'Disengage', tags: ['combat', 'movement'], description: 'Attempt to break from combat and withdraw' },
    { id: 'use', name: 'Use', description: 'Use a program or item' },
    { id: 'speak', name: 'Speak', tags: ['dialogue'], description: 'Talk to an NPC' },
    { id: 'choose', name: 'Choose', tags: ['dialogue'], description: 'Select a dialogue option' },
    { id: 'jack-in', name: 'Jack In', tags: ['netrunning'], description: 'Connect to a network node' },
    { id: 'use-ability', name: 'Use Ability', tags: ['ability'], description: 'Activate a special ability or program' },
  ],
  contentConventions: {
    entityTypes: ['runner', 'npc', 'ice-agent', 'drone', 'program'],
    statusTags: ['buff', 'debuff', 'virus', 'firewall'],
    networkTags: ['node', 'subnet', 'firewall', 'data-vault'],
  },
  progressionModels: [],
};

export { createGameEngine, getDialogueDefinitions as getDialogueMap, saveEngine, getItemById, loaRuleset };