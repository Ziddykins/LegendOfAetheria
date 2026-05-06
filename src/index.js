const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
  mode: 'development',
  entry: './src/index.js',
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'bundle.js',
    clean: true,
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        // This is the key: tell webpack to automatically determine the module type
        type: 'javascript/auto', 
        resolve: {
          fullySpecified: false, // Prevents errors if engine imports don't use extensions
        },
      },
    ],
  },
  plugins: [
    new HtmlWebpackPlugin({
      template: './src/index.php',
      filename: 'index.php',
    }),
  ],
};

