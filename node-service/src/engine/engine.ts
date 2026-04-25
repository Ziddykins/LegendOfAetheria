import {Engine} from '@ai-rpg-engine/core'
import {
	buildCombatStack,
	createDialogueCore,
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
		statMapping: {attack: 'might', precision: 'agility', resolve: 'will'},
		playerId: 'hero',
	});

	const normalizedDialogueDefinitions = getDialogueDefinitions().map(dialogue => ({
		...dialogue,
		speakers: dialogue.speakers ?? [],
	}));

	const dialogueCoreRaw = createDialogueCore([normalizedDialogueDefinitions]);
	const dialogueModules = Array.isArray(dialogueCoreRaw)
		? dialogueCoreRaw
		: [dialogueCoreRaw];

	let lol = new EngineOptions();
	let engine = new Engine();

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
		stats: {might: 6, agility: 5, will: 4},
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
		stats: {might: 4, agility: 6, will: 2},
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
		stats: {might: 4, agility: 1, will: 8},
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
				text: "Welcome. You come seeking quests. Little do you know, you've been on one the minute you walked through that door.",
				choices: [
					{
						text: '...huh?',
						nextNodeId: 'clueless',
						type: {
							color: 'primary',
							icon: 'emoji-astonished-fill'
						}
					},
					{
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
				text: 'Oh, nothing. Here, drink this quest-enabling potion.',
				choices: [
					{
						text: 'You got it, sport-o!',
						nextNodeId: 'end_power',
						type: {
							color: 'success',
							icon: 'emoji-sunglasses-fill'
						}
					},
					{
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
				text: 'You fool... You foolish FOOL! You know what? I don\'t even wanna give you the only potion in the world that enables quests. Go live your questless life without quests, there, No-Quests. I\'ll just be over here, on a quest, questin\' it up.',
				choices: [
					{
						text: 'Wait, the only one? Gimme it or die, old man!',
						nextNodeId: 'reverse_1',
						type: {
							color: 'warning',
							icon: 'bi-award-fill'
						}
					},
					{
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

	return def;
}
