const path = require('path');
const CopyWebpackPlugin = require('copy-webpack-plugin');

function patchRootHeader(content) {
  let text = content;

  // Replace the old Jquery + debug JS block with a single bundle.
  text = text.replace(/<script src="js\/jquery\.js"><\/script>[\s\S]*?<\/script>\s*<\/script>/, '<script src="/js/game.bundle.js"></script>');
  // Remove the old Bootstrap bundle script line because it's now part of the bundle.
  text = text.replace(/<script src="\/node_modules\/bootstrap\/dist\/js\/bootstrap\.bundle\.min\.js"[^>]*><\/script>\s*/, '');

  // Redirect Bootstrap, icons, and RPG Awesome CSS to local copies.
  text = text.replace(/<link rel="stylesheet" type="text\/css" href="\/node_modules\/bootstrap\/dist\/css\/bootstrap\.min\.css">/, '<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">');
  text = text.replace(/<link rel="stylesheet" href="\/node_modules\/bootstrap-icons\/font\/bootstrap-icons\.min\.css">/, '<link rel="stylesheet" href="/css/bootstrap-icons.min.css">');
  text = text.replace(/<link rel="stylesheet" type="text\/css" href="\.\/node_modules\/rpg-awesome\/css\/rpg-awesome\.min\.css" \/>/, '<link rel="stylesheet" type="text/css" href="/css/rpg-awesome.min.css" />');

  return text;
}

function patchRootFooter(content) {
  let text = content;

  text = text.replace(/<script src="\/admini\/strator\/js\/adminlte\.min\.js"[\s\S]*?<script type="text\/javascript" src="\/js\/floating-ui-dom\.js"><\/script>/, '<script src="/admini/strator/js/adminlte.min.js" type="text/javascript"></script>\n        <script src="/js/game.bundle.js"></script>');

  return text;
}

module.exports = {
  entry: './build/entry.js',
  output: {
    filename: 'js/game.bundle.js',
    path: path.resolve(__dirname, 'dist'),
    clean: true,
    publicPath: '/',
  },
  resolve: {
    extensions: ['.js', '.json'],
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        type: 'javascript/auto',
      },
    ],
  },
  plugins: [
    new CopyWebpackPlugin({
      patterns: [
        { from: 'html', to: 'html', transform(content, absoluteFrom) {
            return patchRootHeader(content.toString());
          }, filter: async (resourcePath) => path.basename(resourcePath) !== 'footers.html' },
        { from: 'html/footers.html', to: 'html/footers.html', transform(content) {
            return patchRootFooter(content.toString());
          } },
        { from: 'pages', to: 'pages' },
        { from: 'admini', to: 'admini' },
        { from: 'css', to: 'css' },
        { from: 'img', to: 'img' },
        { from: 'chat', to: 'chat' },
        { from: 'api', to: 'api' },
        { from: 'navs', to: 'navs' },
        { from: 'system', to: 'system' },
        { from: 'vendor', to: 'vendor' },
        { from: 'src', to: 'src' },
        { from: 'node/index.js', to: 'node/index.js' },
        { from: 'node/package.json', to: 'node/package.json' },
        { from: 'node/package-lock.json', to: 'node/package-lock.json' },
        { from: 'node/src', to: 'node/src' },
        { from: 'node_modules/bootstrap/dist/css/bootstrap.min.css', to: 'css/bootstrap.min.css' },
        { from: 'node_modules/bootstrap-icons/font/bootstrap-icons.min.css', to: 'css/bootstrap-icons.min.css' },
        { from: 'node_modules/rpg-awesome/css/rpg-awesome.min.css', to: 'css/rpg-awesome.min.css' },
        { from: '*.php', to: '[name][ext]' },
        { from: '*.html', to: '[name][ext]' },
        { from: '.htaccess', to: '.htaccess' }
      ],
    }),
  ],
};
