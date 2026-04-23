import { Engine, EngineOptions, WorldStore } from '@ai-rpg-engine/core';
import * as fs from 'node:fs';
const SAVE_FILE = './loa.eng';

export async function loadGame() {
	try {
		return await fs.readFileSync(SAVE_FILE, 'utf8');
	} catch {
		return null;
	}
}

export async function saveGame(engine: Engine) {
	const data = JSON.stringify(engine);
    await fs.writeFileSync(SAVE_FILE, data, 'utf8');
}

export function saveEngine(engine: Engine): string {
	return JSON.stringify({
		world: engine.store.serialize(), // KEEP AS STRING
		actionLog: engine.getActionLog(),
	});
}

export function loadEngine(save: string, options: EngineOptions): Engine {
	const tmp_engine = new Engine({
		manifest: {
			id: 'loa',
			title: 'Legend of Aetheria',
			version: '1.0.0',
			engineVersion: '2.3.1',
			ruleset: loaRuleset,
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

  const parsed = JSON.parse(save);
  const restoredStore = WorldStore.deserialize(
    parsed.world,
    (tmp_engine.store as any).events
  );

  (tmp_engine as any).store = restoredStore;
  (tmp_engine as any).actionLog = parsed.actionLog ?? [];

  return tmp_engine;
}
