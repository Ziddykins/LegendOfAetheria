import { Engine, WorldStore } from '@ai-rpg-engine/core';
import { RulesetDefinition } from '@ai-rpg-engine/core';

import {
	buildCombatStack,
	traversalCore,
	statusCore,
	createDialogueCore,
} from '@ai-rpg-engine/modules';

import * as fs from 'node:fs';
import { loaRuleset } from '../engine/ruleset.js';

const SAVE_FILE = '../loa.eng';

export function loadGame() {
	try {
		return fs.readFileSync(SAVE_FILE, 'utf8');
	} catch {
		return null;
	}
}

export function saveGame(engine: Engine) {
	const data = JSON.stringify(engine);
    fs.writeFileSync(SAVE_FILE, data, 'utf8');
}

export function saveEngine(engine: Engine): string {
	return JSON.stringify({
		world: engine.store.serialize(), // KEEP AS STRING
		actionLog: engine.getActionLog(),
	});
}

export function loadEngine(): Engine {
	const tmp_engine = new Engine({
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
			...dialogueModules,
		],
	});

  const parsed = JSON.parse(loadGame());
  const restoredStore = WorldStore.deserialize(
    parsed.world,
    (tmp_engine.store as any).events
  );

  (tmp_engine as any).store = restoredStore;
  (tmp_engine as any).actionLog = parsed.actionLog ?? [];

  return tmp_engine;
}