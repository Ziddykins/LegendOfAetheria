import express, { json, text, urlencoded } from 'express';
import cookieParser from 'cookie-parser';
import log from 'morgan';
import cors from 'cors';
import multer from 'multer';
import helmet from 'helmet';
import authRouter from './routes/auth.route.js';
import v1Router from './routes/v1.route.js';

const upload = multer();
const app = express();
const PORT = process.env.PORT || 3000;
const NODE_ENV = process.env.NODE_ENV || 'development';

app.set('port', PORT);
app.set('env', NODE_ENV);

app.use(cors());
app.use(log('tiny'));
app.use(helmet());

// parse application/json
app.use(json());

// parse raw text
app.use(text());

// parse application/x-www-form-urlencoded
app.use(urlencoded({ extended: true }));
app.use(cookieParser());

// parse multipart/form-data
app.use(upload.array());

async function initGameEngine() {
    try {
        app.use('/auth', authRouter);
        app.use('/v1', v1Router);

        app.listen(PORT, () => {
            console.log('🚀 Server running at http://localhost:' + PORT);
        });
    } catch (err) {
        console.error('❌ Failed to start server:', err);
        process.exit(1);
    }
}

initGameEngine().catch((err) => {
    console.error('Failed to initialize game engine:', err);
    process.exit(1);
});

// catch 404
app.use((req, res, next) => {
    //log.error(`Error 404 on ${req.url}.`);
    res.status(404).send({ status: 404, error: 'Not found' });
});

// catch errors
app.use((err, req, res, next) => {
    const status = err.status || 500;
    const msg = err.error || err.message;
    //log.error(`Error ${status} (${msg}) on ${req.method} ${req.url} with payload ${req.body}.`);
    res.status(status).json({ status, error: msg });
});

export default app;
