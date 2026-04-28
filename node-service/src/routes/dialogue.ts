import express from 'express';
import type { Request, Response, NextFunction } from 'express';
import { saveEngine } from '../utils/save.js';

export default function dialogueRoutes(engine: any, dialogueMap: Record<string, any>, saveEngine: any) {
	const router = express.Router();

	router.use((req: Request, res: Response, next: NextFunction) => {
		console.debug(`===REQ===\nURL: ${req.originalUrl}\n${JSON.stringify(req.body)}\n===`);
		next();
	});

	router.post('/talk/:npcId{/:nodeId}', async (req: Request, res: Response) => {
		const npcId = req.params.npcId;
		const nodeId = req.params.nodeId;

		const dialogue = Object.values(dialogueMap as Record<string, any>).find(
			d => (d as any).ownerId === npcId
		);

		if (!dialogue) {
			return res.json({text: "NPC has nothing to say."});
		}

		if (typeof nodeId !== 'undefined' && nodeId !== 'reset') {
			dialogue.startNodeId = nodeId;
		}

		engine.store.state.dialogueCore = {
			activeDialogueId: dialogue.id,
			activeNodeId: dialogue.startNodeId,
			npcId
		};

		saveEngine(engine);

		const node = dialogue.nodes[dialogue.startNodeId];

		console.debug(`===RES===\nURL: ${JSON.stringify({text: node.text, choices: node.choices || []})}\n===`);
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

		await saveEngine(engine);

		res.json({
			text: nextNode.text,
			choices: nextNode.choices || [],
			end: !!nextNode.end
		});
	});

	return router;
}
