import { getDialogueDefinitions } from './services/loa.js';
import express from 'express';
import cookieParser from 'cookie-parser';
import morgan from 'morgan';
import path from 'path';
import cors from 'cors';
import multer from 'multer';
import helmet from 'helmet';
import mysql from 'mysql';
import csurf from 'csurf';
import { createServer } from 'http';

// Environment variables
const PORT = process.env.PORT || 3000;
const NODE_ENV = process.env.NODE_ENV || 'development';

const app = express();
const server = createServer(app);

app.set('port', PORT);
app.set('env', NODE_ENV);

app.use(cors());
app.use(morgan('tiny'));
app.use(helmet());

// parse application/json
app.use(json());

// parse raw text
app.use(text());

// parse application/x-www-form-urlencoded
app.use(urlencoded({ extended: true }));
app.use(cookieParser());

// parse multipart/form-data
const upload = multer();
app.use(upload.array());

// parse cookies
app.use(cookieParser());

// serve static files
app.use(express.static('public'));

// Game engine imports
let engine;

/**
 * Dynamically import and instantiate the game engine.
 */
async function createGameEngine() {
  const { Engine } = await import('@ai-rpg-engine/core');
  return new Engine();
}

/**
 * Build a map of dialogue definitions keyed by their id.
 */
async function getDialogueMap() {
  const definitions = getDialogueDefinitions();
  return definitions.reduce((map, def) => {
    map[def.id] = def;
    return map;
  }, {});
}

/**
 * Initialise the engine and mount all route handlers.
 */
async function initGameEngine() {
  try {
    const dialogueMap = await getDialogueMap();
    engine = await createGameEngine();

    // Import route factories as ES modules
    const { default: dialogueRoutes } = await import('./routes/dialogue.js');
    const { default: combatRoutes } = await import('./routes/combat.js');
    const { default: engineState } = await import('./routes/state.js');
    const { default: itemRoutes } = await import('./routes/item.js');
    const { default: saveEngine } = await import('./utils/save.js');

    // Mount routes
    app.use('/', dialogueRoutes(engine, dialogueMap, saveEngine));
    app.use('/', combatRoutes(engine));
    app.use('/', engineState(engine, saveEngine));
    app.use('/', itemRoutes(engine));

    // Start listening
    server.listen(PORT, () => {
      console.log(`🚀 Server running at http://localhost:${PORT}`);
    });
  } catch (err) {
    console.error('❌ Failed to start server:', err);
    process.exit(1);
  }
}

// Kick off the initialisation
initGameEngine().catch((err) => {
  console.error('Failed to initialize game engine:', err);
  process.exit(1);
});

/**
 * Additional route initialisation (kept for compatibility with the original file).
 * This will start a second listener if the above already started one,
 * so we guard against that by checking if the server is already listening.
 */
async function initializeRoutes() {
  if (!server.listening) {
    server.listen(PORT, () => {
      console.log(
        `Express Server started on Port ${app.get('port')} | Environment : ${app.get('env')}`
      );
    });
  }
}

initializeRoutes().catch((err) => {
  console.error('Failed to initialize API routes:', err);
  process.exit(1);
});

// 404 handler
app.use((req, res) => {
  res.status(404).json({ status: 404, error: 'Not found' });
});

// Global error handler
app.use((err, req, res, next) => {
  const status = err.status || 500;
  const msg = err.error || err.message;
  res.status(status).json({ status, error: msg });
});

export default app;
