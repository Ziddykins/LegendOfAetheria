import express from 'express';
import {createGameEngine, getDialogueMap} from './src/engine/engine.js';
import {loadGame, saveGame} from './src/utils/save.js';
import {engineState, debugOutput} from './src/routes/state.js';
import dialogueRoutes from './src/routes/dialogue.js';
import combatRoutes from './src/routes/combat.js';
import {dialogueMap, dialogueRoutes} from '@ai-rpg-engine/modules'
const app = express();
app.use(express.json());

let engine;

async function init() {
	try {
		const saveData = loadGame();
		const dialogueMap = getDialogueMap();
		const saveTest = loadEngine();

		console.log(saveTest);
		engine = await createGameEngine(saveData);

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
