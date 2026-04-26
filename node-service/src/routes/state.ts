import express, { Request, Response } from 'express';

export default function engineState(engine: any) {
	const router = express.Router();

	router.post('/state', async (req: Request, res: Response) => {
		try {
			res.send(engine.serialize());
		} catch (err: unknown) {
			res.status(500).json({ error: err instanceof Error ? err.message : String(err) });
		}
	});

	return router;
}
