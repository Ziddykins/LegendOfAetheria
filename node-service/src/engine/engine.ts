import {Engine} from '@ai-rpg-engine/core'
import {
	buildCombatStack,
	createDialogueCore,
	traversalCore,
	statusCore,
} from '@ai-rpg-engine/modules';

import {loadEngine, saveEngine} from '../utils/save.js';

export function getDialogueMap() {
	return dialogueMap;
}

let dialogueDefinitions = getDialogueDefinitions();
let dialogueMap = Object.fromEntries(
	dialogueDefinitions.map(d => [d.id, d])
);

export async function createGameEngine(existingData = null) {
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
			version: '1.0.0',
			engineVersion: '2.3.1',
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

function getDialogueDefinitions() {
	let def = {
		id: 'sage_intro',
		ownerId: 'question-sage',
		startNodeId: 'start',
		entryNodeId: 'start',
		speakers: ['Question Sage'],


		nodes: {
			start: {
				id: 'start',
				speaker: 'Question Sage',
				text: "Welcome. You come seeking quests. Little do you know, you've been on one the minute you walked through that door.",
				choices: [
					{
					id: 'choice-1',
						text: '...huh?',
						nextNodeId: 'clueless',
						type: {
							color: 'primary',
							icon: 'emoji-astonished-fill'
						}
					},
					{
					id: 'choice-2',
						text: 'Shut it, old man! Out with the quests or die.',
						nextNodeId: 'rude',
						type: {
							color: 'danger',
							icon: 'emoji-angry-fill'
						}
					},
				],
			},

			clueless: {
				id: 'clueless',
				speaker: 'Question Sage',
				text: 'Oh, nothing. Here, drink this quest-enabling potion.',
				choices: [
					{
					id: 'choice-3',
						text: 'You got it, sport-o!',
						nextNodeId: 'end_power',
						type: {
							color: 'success',
							icon: 'emoji-sunglasses-fill'
						}
					},
					{
					id: 'choice-4',
						text: "I ain't quaffin' a thing, later creep-o",
						nextNodeId: 'reverse_1',
						type: {
							color: 'warning',
							icon: 'emoji-neutral-fill'
						}
					},
				],
			},

			rude: {
				id: 'rude',
				speaker: 'Question Sage',
				text: 'You fool... You foolish FOOL! You know what? I don\'t even wanna give you the only potion in the world that enables quests. Go live your questless life without quests, there, No-Quests. I\'ll just be over here, on a quest, questin\' it up.',
				choices: [
					{
					id: 'choice-5',
						text: 'Wait, the only one? Gimme it or die, old man!',
						nextNodeId: 'reverse_1',
						type: {
							color: 'warning',
							icon: 'bi-award-fill'
						}
					},
					{
					id: 'choice-6',
						text: 'Well, can you sweeten the deal. Toss in another potuon maybe?',
						nextNodeId: 'bargain',
						type: {
							color: 'success',
							icon: 'bi-flask-florence-fill'
						}
					}
				]
			},

			end_power: {
				id: 'end_power',
				speaker: 'Question Sage',
				text: 'Good, good. Now we play the waiting game... +2 STR.',
				effects: [
					{
						type: 'modifyStat',
						targetId: 'hero',
						stat: 'strength',
						amount: 2,
					},
				],
				end: true,
			},

			bargain: {
				id: 'bargain',
				speaker: 'Question Sage',
				text: 'Hrmph... Let me check the back room. Ah, yes, fine. Here.',
				effects: [
					{
						type: 'modifyStat',
						targetId: 'hero',
						stat: 'might',
						amount: 2,
					},
				],
			},
		},
	}

	return [def];
}
