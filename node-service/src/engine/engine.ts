import {Engine, WorldStore} from '@ai-rpg-engine/core'
import {
	buildCombatStack,
	createDialogueCore,
	traversalCore,
	statusCore,
} from '@ai-rpg-engine/modules';

import { loaRuleset } from './ruleset.js'
import { saveEngine } from '../utils/save.js';
import { getDialogueDefinitions } from './dialogueData.js';
import * as fs from 'node:fs'

export function getDialogueMap() {
	return dialogueMap;
}

function loadEngine() {
	const contents = fs.readFileSync('loa.eng', 'utf8').trim();
	const dialogueModule = createDialogueCore([] as any);
	const combatStack = buildCombatStack({
		statMapping: {attack: 'strength', precision: 'dexterity', resolve: 'intelligence'},
		playerId: 'hero',
	});

	let tmp_engine;

	if (contents && contents !== 'undefined' || contents !== 'null') {
		tmp_engine = new Engine({
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
	    const restoredStore = WorldStore.deserialize(
			parsed.world,
			(tmp_engine.store as any).events
		);

		(tmp_engine as any).store = restoredStore;
		(tmp_engine as any).actionLog = parsed.actionLog ?? [];
	}
	
	return (tmp_engine as any);
}

let dialogueDefinitions = getDialogueDefinitions();
let dialogueMap = Object.fromEntries(
	dialogueDefinitions.map(d => [d.id, d])
);

export async function createGameEngine() {
	const combatStack = buildCombatStack({
		statMapping: {attack: 'strength', precision: 'intelligence', resolve: 'defense'},
		playerId: 'hero',
	});

	const dialogueDefinitions = getDialogueDefinitions();	
	const normalizedDialogueDefinitions = dialogueDefinitions.map(dialogue => ({
		...dialogue,
		speakers: dialogue.speakers ?? [],
	}));

	const dialogueCoreRaw = createDialogueCore(normalizedDialogueDefinitions as any);
	const dialogueModules = Array.isArray(dialogueCoreRaw)
		? dialogueCoreRaw
		: [dialogueCoreRaw];

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
function bootstrapWorld(engine: Engine) {
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
		stats: {strength: 6, defense: 5, intelligence: 4},
		resources: {hp: 25},
		statuses: [],
		blueprintId: "",
		tags: [""]
	});

	engine.store.state.playerId = 'hero';
	engine.store.state.locationId = 'sanctuary';
	engine.store.state


	engine.store.addEntity({
		id: 'wolf',
		type: 'enemy',
		name: 'Wolf',
		stats: {strength: 4, defense: 6, intelligence: 2},
		resources: {hp: 12},
		statuses: [],
		zoneId: 'forest',
		blueprintId: 'melee',
		tags: []
	});

	engine.store.addEntity({
		id: 'question-sage',
		type: 'npc',
		name: 'QUESTion Sage',
		tags: ['wise', 'old'],
		zoneId: 'sanctuary',
		blueprintId: "",
		resources: {hp: 10},
		stats: {strength: 4, defense: 1, intelligence: 8},
		statuses: []
	});
}
