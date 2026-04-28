import { Engine, WorldStore } from '@ai-rpg-engine/core';

import {
	buildCombatStack,
	traversalCore,
	statusCore,
	createDialogueCore,
} from '@ai-rpg-engine/modules';

import * as fs from 'node:fs';
import { loaRuleset } from '../engine/ruleset.js';

const SAVE_FILE = '/data/data/com.termux/files/usr/share/apache2/default-site/htdocs/node-service/loa.eng';

export function loadEngine() {
	const contents = fs.readFileSync(SAVE_FILE, 'utf8').trim();
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

	saveEngine((tmp_engine as any));
	return (tmp_engine as any);
}

export function saveEngine(engine: Engine): string {
	let engine_str = JSON.stringify({
		world: engine.store.serialize(), // KEEP AS STRING
		actionLog: engine.getActionLog(),
	});

	fs.writeFileSync(SAVE_FILE, engine_str, 'utf8');
	return engine_str;
}



