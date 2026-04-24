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

	const normalizedDialogueDefinitions = dialogueDefinitions.map(dialogue => ({
		...dialogue,
		speakers: dialogue.speaker ?? [],
	}));

	const dialogueCoreRaw = createDialogueCore(normalizedDialogueDefinitions);
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