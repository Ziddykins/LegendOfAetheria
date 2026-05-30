import authRouter from './routes/auth.route.js';
import v1Router from './routes/v1.route.js';

export default function (app) {
    app.use('/auth', authRouter);
    app.use('/v1', v1Router);
};
