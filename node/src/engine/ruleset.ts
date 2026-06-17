import type { RulesetDefinition } from '@ai-rpg-engine/core';

export const loaRuleset: RulesetDefinition = {
	id: 'loa-rs',
	name: 'LoA Ruleset',
	version: '1.0.0',

	stats: [
		{ id: 'strength', name: 'Strength', min: 1, default: 6 },
		{ id: 'defense', name: 'Defense', min: 1, default: 5},
		{ id: 'intelligence', name: 'Intelligence', min: 1, default: 4 },
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