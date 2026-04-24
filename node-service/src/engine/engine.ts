import { Engine, RulesetDefinition } from '@ai-rpg-engine/core';
import {
	buildCombatStack,
	traversalCore,
	statusCore,
	createDialogueCore,
} from '@ai-rpg-engine/modules';

import { saveGame, saveEngine, loadEngine } from '../utils/save.js';

// build lookup map
let dialogueMap = Object.fromEntries(
	dialogueDefinitions.map(d => [d.id, d])
);

export function getDialogueMap() {
	return dialogueMap;
}

export async function createGameEngine(existingData = null) {
	const combatStack = buildCombatStack({
		statMapping: { attack: 'might', precision: 'agility', resolve: 'will' },
		playerId: 'hero',
	});

	const dialogueCoreRaw = createDialogueCore(dialogueDefinitions);
	const dialogueModules = Array.isArray(dialogueCoreRaw)
		? dialogueCoreRaw
		: [dialogueCoreRaw];

	let engine;

	try {
		engine = loadEngine();
		console.log('📂 Save loaded');
	} catch (err) {
		console.warn('⚠️ Failed to load save, creating new world: ' + err);
		bootstrapWorld(engine);
	}

	saveEngine(engine);

	return engine;
}

// ----------------------
// WORLD SETUP
// ----------------------
function bootstrapWorld(engine) {
	engine.store.addZone({
		id: 'sanctuary',
		name: 'Sanctuary',
		tags: ['safe'],
		neighbors: ['forest'],
	});

	engine.store.addZone({
		id: 'forest',
		name: 'Dark Forest',
		tags: ['danger'],
		neighbors: ['sanctuary'],
	});

	engine.store.addEntity({
		id: 'hero',
		type: 'player',
		name: 'Hero',
		stats: { might: 6, agility: 5, will: 4 },
		resources: { hp: 25 },
		statuses: [],
	});

	engine.store.state.playerId = 'hero';
	engine.store.state.locationId = 'sanctuary';

	engine.store.addEntity({
		id: 'wolf',
		type: 'enemy',
		name: 'Wolf',
		stats: { might: 4, agility: 6, will: 2 },
		resources: { hp: 12 },
		statuses: [],
		zoneId: 'forest',
	});

	engine.store.addEntity({
		id: 'question-sage',
		type: 'npc',
		name: 'QUESTion Sage',
		tags: ['wise', 'old'],
		zoneId: 'sanctuary',
	});
}

const loaRuleset: RulesetDefinition = {
	id: 'loa-rs',
	name: 'LoA Ruleset',
	version: '1.0.0',

	stats: [
		{ id: 'strength', name: 'Strength', min: 10, default: 10 },
		{ id: 'defense', name: 'Defense', min: 10, default: 10},
		{ id: 'intelligence', name: 'Intelligence', min: 10, default: 10 },
	],
	  
	resources: [
		{ id: 'hp', name: 'Health', min: 0, default: 10 },
		{ id: 'mp', name: 'Mana', min: 0, default: 10 },
		{ id: 'en', name: 'Energy', min: 0, default: 10, regenRate: 2 },
	],

	defaultModules: [
	    'traversal-core',
		'status-core',
	    'combat-core',
	    'inventory-core',
		'dialogue-core',
	],

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
	    { id: 'inspect', name: 'Scan', description: 'Scan current node or target' },
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

}
