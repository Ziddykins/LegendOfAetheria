import express from 'express';
import auth from '../services/auth.js';
import jwt from 'jsonwebtoken';

const router = express.Router();

function parseCredentials(req) {
    const authHeader = req.headers.authorization;
    if (authHeader && authHeader.startsWith('Basic ')) {
        const payload = atob(authHeader.split(' ')[1]);
        const [email, password] = payload.split(':');
        return { email, password };
    } else 

        if (jwt.verify(token, ))
        return { token: token };
    }

    if (req.body && req.body.email && req.body.password) {
        return {
            email: req.body.email,
            password: req.body.password
        };
    }

    return null;
}

async function handleBearer(req, res) {
    const authHeader = req.headers.authorization;

    if (authHeader && authHeader.startsWith('Bearer ')) {
        const token = await auth.checkToken(authHeader.split(' ')[1]);

        
        
}

async function handleLogin(req, res) {
    const options = parseCredentials(req);

    if (!options) {
        return res.status(400).json({ error: 'Missing email, password or token' });
    }

    if (!options.token) {
        try {
            const result = await auth.postAuthorization(options);

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
}

router.get('/', async (req, res) => {
    res.json({ message: 'Auth endpoint. POST credentials to /auth or /auth/basic.' });
});

router.post('/', handleLogin);
router.post('/basic', handleLogin);
router.post('/refresh', handleBearer);

export default router;