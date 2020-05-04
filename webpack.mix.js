const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
  .js('resources/js/dashboard', 'public/js')
  .sass('resources/sass/app.scss', 'public/css')
  .sass('resources/sass/dashboard.scss', 'public/css');

mix.disableNotifications();
