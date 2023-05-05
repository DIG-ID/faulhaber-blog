// webpack.mix.js

// webpack.mix.js

let mix = require('laravel-mix');

mix
  .setPublicPath('public/dist')
  .js('public/js/faulhaber-blog-public.js', 'js')
  .sass('public/sass/faulhaber-blog-public.sass', 'css')
  .disableNotifications()


if (!mix.inProduction()) {
  mix
    .webpackConfig({
      devtool: "source-map"
    })
    .sourceMaps();
}

if (mix.inProduction()) {
  mix.version();
}