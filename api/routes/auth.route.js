import express from 'express';
import auth from '../services/auth.js';
const router = express.Router();

function parseCredentials(req) {
    const authHeader = req.headers.authorization;
    if (authHeader && authHeader.startsWith('Basic ')) {
        const payload = atob(authHeader.split(' ')[1]);
        const [email, password] = payload.split(':');
        return { email, password };
    }

    if (req.body && req.body.email && req.body.password) {
        return { email: req.body.email, password: req.body.password };
    }

    return null;
}

async function handleLogin(req, res) {
    const options = parseCredentials(req);
    if (!options || !options.email || !options.password) {
        return res.status(400).json({ error: 'Missing email or password' });
    }

    try {
        const result = await auth.postBasic(options);
        
        // Check if auth service returned an error
        if (result.data.error) {
            const status = result.data.status || 500;
            return res.status(status).json({ error: result.data.error });
        }
        
        return res.status(result.data.status || 200).json(result.data);
    } catch (err) {
        console.error('Auth route error:', err);
        return res.status(500).json({ error: 'Internal server error' });
    }
}

router.post('/', handleLogin);
router.post('/basic', handleLogin);

router.get('/', async (req, res) => {
    res.json({ message: 'Auth endpoint. POST credentials to /auth or /auth/basic.' });
});

router.post('/refresh', async (req, res, next) => {
    res.status(501).json({ error: 'Refresh endpoint not implemented' });
});

export default router;