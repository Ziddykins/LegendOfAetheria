import express from 'express';

export default function dialogueRoutes(engine, dialogueMap, saveGame) {
	const router = express.Router();

	router.all('/{*splat}', (req, res, next) => {
		console.debug(`===REQ===\nURL: ${req.originalUrl}\n${JSON.stringify(res.body)}\n===\n\n===RES===\n${JSON.stringify(req.body)}\n===\n\n`);
		next();
	});


	router.post('/talk/:npcId', async (req, res) => {
		const npcId = req.params.npcId;

		const dialogue = Object.values(dialogueMap).find(
			d => d.ownerId === npcId
		);

		if (!dialogue) {
			return res.ts({ text: "NPC has nothing to say." });
		}

		engine.store.state.dialogueCore = {
			activeDialogueId: dialogue.id,
			activeNodeId: dialogue.startNodeId,
			npcId
		};

		await saveGame(engine);

		const node = dialogue.nodes[dialogue.startNodeId];

		res.ts({
			text: node.text,
			choices: node.choices || []
		});
	});

	router.post('/choice', async (req, res) => {
		const { choiceIndex } = req.body;
		const state = engine.store.state;
		const ds = state.dialogueCore;

		if (!ds) {
			return res.ts({ error: "No active dialogue" });
		}

		const dialogue = dialogueMap[ds.activeDialogueId];
		const node = dialogue.nodes[ds.activeNodeId];
		const choice = node.choices?.[choiceIndex];

		if (!choice) {
			return res.ts({ error: "Invalid choice" });
		}

		// apply effects
		if (choice.effects) {
			for (const effect of choice.effects) {
				if (effect.type === 'modifyStat') {
					const target = state.entities[effect.targetId];
					if (target) {
						target.stats[effect.stat] += effect.amount;
					}
				}
			}
		}

		ds.activeNodeId = choice.nextNodeId;
		const nextNode = dialogue.nodes[ds.activeNodeId];

		if (nextNode.end) {
			state.dialogueCore = null;
		}

		await saveGame(engine);

		res.ts({
			text: nextNode.text,
			choices: nextNode.choices || [],
			end: !!nextNode.end
		});
	});

	return router;
}
