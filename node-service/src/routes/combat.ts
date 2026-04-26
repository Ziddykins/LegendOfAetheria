import express, { Request, Response } from 'express';

export default function combatRoutes(engine: any, saveGame: any) {
	const router = express.Router();

	router.post('/attack/:targetId', async (req: Request, res: Response) => {
		try {
			engine.submitAction('attack', {
				targetIds: [req.params.targetId],
			});

			await saveGame(engine);

			res.json(engine.store.state);
		} catch (err: unknown) {
			res.status(500).json({ error: err instanceof Error ? err.message : String(err) });
		}
	});

	return router;
}
