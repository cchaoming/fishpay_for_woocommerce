const path = require('path');

module.exports = {
  entry: ['./src/fishpay_wx.js','./src/fishpay_alipay.js'], // 入口文件
  output: {
    filename: 'fishpay.js',
     path: path.resolve(__dirname, 'build'), // 打包后的文件存放路径
  },
  module: {
    rules: [
      {
        test:  /\.(js|jsx)$/, // 用正则匹配.js文件
        exclude: /node_modules/, // 排除node_modules目录
        use: {
          loader: 'babel-loader', // 使用babel-loader
          options: {
            presets: ['@babel/preset-env', '@babel/preset-react'] // 使用babel预设
          }
        }
      },
    ]
  },
};