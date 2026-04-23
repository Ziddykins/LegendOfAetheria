import express from 'express';

export default function engineState(engine) {
	const router = express.Router();

	router.post('/state', async (req, res) => {
		try {
			res.send(engine.serialize());
		} catch (err) {
			res.status(500).json({ error: err.message });
		}
	});

	return router;
}
