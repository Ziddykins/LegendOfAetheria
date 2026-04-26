import express from 'express';
import {createGameEngine, getDialogueMap} from './src/engine/engine.ts';
import {saveGame} from './src/utils/save.ts';
import engineState from './src/routes/state.ts';
import dialogueRoutes from './src/routes/dialogue.ts';
import combatRoutes from './src/routes/combat.ts';
const app = express();
app.use(express.json());

let engine;

async function init() {
	try {
		const dialogueMap = getDialogueMap();
		engine = await createGameEngine();

		app.use('/', dialogueRoutes(engine, dialogueMap, saveGame));
		app.use('/', combatRoutes(engine, saveGame));
		app.use('/', engineState(engine));

		app.listen(3000, () => {
			console.log('🚀 Server running at http://localhost:3000');
		});


	} catch (err) {
		console.error('❌ Failed to start server:', err);
		process.exit(1);
	}
}

init();
