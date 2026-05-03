import express from 'express';
import type { Request, Response } from 'express';
import type { Engine } from '@ai-rpg-engine/core';
export default function engineState(engine: Engine) {
	const router = express.Router();

	router.post('/state', async (req: Request, res: Response, saveEngine) => {
		try {
			const engine_str = JSON.stringify(engine);
			res.send(engine_str)
		} catch (err: unknown) {
			res.status(500).json({ error: err instanceof Error ? err.message : String(err) });
		}
	});

	return router;
}
