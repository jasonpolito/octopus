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
    globs: [path.join(__dirname, "theme/**/*.htm")],
    extensions: ["htm", "vue"],
    whitelistPatterns: [/active/, /^slideout/, /^lazyload/]
  })
  .copy(`./webpack.mix.js`, `../october/themes/${theme}/webpack.mix.js`)
  .version()
  .disableNotifications();

if (mix.inProduction()) {
  // mix.postCss("./public/css/app.css", "public/css", [tailwindcss("./tailwind-config.js")]);
  mix.copy(
    `/Users/jasonpolito/Development/october/themes/${theme}/assets/css/theme.css`,
    `/Users/jasonpolito/Development/october/themes/${theme}/assets/css/theme.less`
  );
  mix.webpackConfig({
    plugins: [
      // new FaviconsWebpackPlugin({
      //   logo: './src/icon.png',
      //   // emitStats: true,
      //   // statsFilename: 'iconstats-[hash].json',
      // }),
      // new CopyWebpackPlugin([{
      //     from: 'public/img', // FROM
      //     to: 'public/images', // TO
      // }]),
      // new ImageminPlugin({
      //     test: /\.(jpe?g|png|gif|svg)$/i,
      //     pngquant: {
      //         quality: '50-75'
      //     },
      //     plugins: [
      //         imageminMozjpeg({
      //             quality: 50,
      //             //Set the maximum memory to use in kbytes
      //             maxMemory: 1000 * 512
      //         })
      //     ]
      // }),
    ]
  });
}
