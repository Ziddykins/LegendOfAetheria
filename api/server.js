const express = require('express'),
    cookieParser = require('cookie-parser'),
    log = require('morgan'),
    path = require('path'),
    cors = require('cors'),
    multer = require('multer'),
    upload = multer(),
    app = express(),
    helmet = require('helmet'),
    mysql = require('mysql'),
    csrf = require('csurf'),
    PORT = process.env.PORT || 3000,
    NODE_ENV = process.env.NODE_ENV || 'development';


app.set('port', PORT);
app.set('env', NODE_ENV);

app.use(cors());
app.use(log('tiny'));
app.use(helmet());

// parse application/json
app.use(express.json());

// parse raw text
app.use(express.text());

// parse application/x-www-form-urlencoded
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());

// parse multipart/form-data
app.use(upload.array());
app.use(express.static('public'));

// Game engine imports
let engine;

async function createGameEngine() {
    const { Engine } = await import('@ai-rpg-engine/core');
    return new Engine();
}

async function getDialogueMap() {
    const { getDialogueDefinitions } = await import('@ai-rpg-engine/modules');
    const definitions = getDialogueDefinitions();
    return definitions.reduce((map, def) => {
        map[def.id] = def;
        return map;
    }, {});
}

async function initGameEngine() {
    try {
        const dialogueMap = await getDialogueMap();
        engine = await createGameEngine();

        // Routes from node/index.js
        const dialogueRoutes = require('./src/routes/dialogue');
        const combatRoutes = require('./src/routes/combat');
        const engineState = require('./src/routes/state');
        const itemRoutes = require('./src/routes/item');
        const saveEngine = require('./src/utils/save');

        app.use('/', dialogueRoutes(engine, dialogueMap, saveEngine));
        app.use('/', combatRoutes(engine));
        app.use('/', engineState(engine, saveEngine));
        app.use('/', itemRoutes(engine));

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

async function initializeRoutes() {
  await routes(app);
  app.listen(PORT, () => {
      console.log(
          `Express Server started on Port ${app.get('port')} | Environment : ${app.get('env')}`
      );
  });
}

initializeRoutes().catch((err) => {
  console.error('Failed to initialize API routes:', err);
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

module.exports = app;
