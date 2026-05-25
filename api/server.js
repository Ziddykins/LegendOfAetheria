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
    routes = require('./routes'),
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

app.listen(PORT, () => {
    console.log(
        `Express Server started on Port ${app.get('port')} | Environment : ${app.get('env')}`
    );
});