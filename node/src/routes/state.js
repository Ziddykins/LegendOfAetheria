import express from 'express';
export default function engineState(engine) {
    const router = express.Router();
    router.post('/state', async (req, res, saveEngine) => {
        try {
            const engine_str = JSON.stringify(engine);
            res.send(engine_str);
        }
        catch (err) {
            res.status(500).json({ error: err instanceof Error ? err.message : String(err) });
        }
    });
    return router;
}
//# sourceMappingURL=state.js.map