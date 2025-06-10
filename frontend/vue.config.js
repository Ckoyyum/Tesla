const { defineConfig } = require("@vue/cli-service");

module.exports = defineConfig({
  transpileDependencies: [],

  // Remove the entire 'css' block that was here
  // css: {
  //   loaderOptions: {
  //     postcss: {
  //       postcssOptions: {
  //         plugins: [require("tailwindcss"), require("autoprefixer")],
  //       },
  //     },
  //   },
  // },

  // Development server configuration
  devServer: {
  port: 8080,
  open: true,
  proxy: {
    "/api": {
      target: "http://localhost:8000", // Change this to match your backend port
      changeOrigin: true,
      pathRewrite: { "^/api": "/api" },
    },
  },
},


  // Performance optimization
  chainWebpack: (config) => {
    config.resolve.alias.set("@", require("path").resolve(__dirname, "src"));
  },
});
