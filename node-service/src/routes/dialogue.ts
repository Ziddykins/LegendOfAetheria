import express, { Request, Response, NextFunction } from 'express';

export default function dialogueRoutes(engine: any, dialogueMap: Record<string, any>, saveGame: any) {
	const router = express.Router();

	router.use((req: Request, res: Response, next: NextFunction) => {
		console.debug(`===REQ===\nURL: ${req.originalUrl}\n${JSON.stringify(req.body)}\n===`);
		next();
	});


	router.post('/talk/:npcId', async (req: Request, res: Response) => {
		const npcId = req.params.npcId;

		const dialogue = Object.values(dialogueMap as Record<string, any>).find(
			d => (d as any).ownerId === npcId
		);

		if (!dialogue) {
			return res.json({text: "NPC has nothing to say."});
		}

		engine.store.state.dialogueCore = {
			activeDialogueId: dialogue.id,
			activeNodeId: dialogue.startNodeId,
			npcId
		};

		await saveGame(engine);

		const node = dialogue.nodes[dialogue.startNodeId];

		res.json({
			text: node.text,
			choices: node.choices || []
		});
	});

	router.post('/choice', async (req: Request, res: Response) => {
		const {choiceIndex} = req.body;
		const state = engine.store.state;
		const ds = state.dialogueCore;

		if (!ds) {
			return res.json({error: "No active dialogue"});
		}

		const dialogue = dialogueMap[ds.activeDialogueId];
		const node = dialogue.nodes[ds.activeNodeId];
		const choice = node.choices?.[choiceIndex];

		if (!choice) {
			return res.json({error: "Invalid choice"});
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

		res.json({
			text: nextNode.text,
			choices: nextNode.choices || [],
			end: !!nextNode.end
		});
	});

	return router;
}
