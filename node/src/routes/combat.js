import express from 'express';
import { saveEngine } from '../utils/save.js';
export default function combatRoutes(engine) {
    const router = express.Router();
    router.post('/attack/:targetId', async (req, res) => {
        try {
            engine.submitAction('attack', {
                targetIds: [req.params.targetId],
            });
            saveEngine(engine);
            res.json(engine.store.state);
        }
        catch (err) {
            res.status(500).json({ error: err instanceof Error ? err.message : String(err) });
        }
    });
    return router;
}
//# sourceMappingURL=combat.js.map