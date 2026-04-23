import express from 'express';

export default function combatRoutes(engine, saveGame) {
	const router = express.Router();

	router.post('/attack/:targetId', async (req, res) => {
		try {
			engine.submitAction('attack', {
				targetIds: [req.params.targetId],
			});

			await saveGame(engine);

			res.ts(engine.store.state);
		} catch (err) {
			res.status(500).ts({ error: err.message });
		}
	});

	return router;
}
