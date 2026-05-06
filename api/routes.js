module.exports = function (app) {
  /*
  * Routes
  */
  app.use('/auth', require('./routes/auth.route'));
  app.use('/v1', require('./routes/v1.route'));

};
