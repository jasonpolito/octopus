const theme = process.env.THEME;
const FaviconsWebpackPlugin = require("favicons-webpack-plugin");
const ImageminPlugin = require("imagemin-webpack-plugin").default;
const CopyWebpackPlugin = require("copy-webpack-plugin");
const imageminMozjpeg = require("imagemin-mozjpeg");
const mix = require("laravel-mix");
const tailwindcss = require("tailwindcss");
const glob = require("glob-all");
require("laravel-mix-purgecss");

class TailwindExtractor {
  static extract(content) {
    return content.match(/[A-z0-9-:\/]+/g) || [];
  }
}

mix.webpackConfig({
  resolve: {
    symlinks: true
  }
});

mix
  .setPublicPath(`../october/themes/${theme}/assets/`)
  .sass(
    `../../Sites/${theme}/src/sass/theme.scss`,
    `public/css/theme.${mix.inProduction() ? "css" : "less"}`
  )
  .js(`../../Sites/${theme}/src/js/app.js`, `public/js/app.js`)
  .options({
    processCssUrls: false,
    postCss: [
      tailwindcss("./tailwind-config.js"),
      require("postcss-discard-comments")({ removeAll: true })
    ]
  })
  .purgeCss({
    enabled: mix.inProduction(),
    globs: ["../**/*.htm"],
    extensions: ["htm", "vue"],
    whitelistPatterns: [/active/, /^slideout/, /^lazyload/]
  })
  // .copy(`./webpack.mix.js`, `../october/themes/${theme}/webpack.mix.js`)
  .version()
  .disableNotifications();

if (mix.inProduction()) {
  // mix.webpackConfig({
  //   plugins: [
  //     new FaviconsWebpackPlugin({
  //       logo: './src/icon.png',
  //     }),
  //   ]
  // });
}
