const mix = require('laravel-mix');

mix.setPublicPath('client/dist');
mix.setResourceRoot('../');

mix.sass('client/src/styles/oembeddable.scss', 'styles');

mix.js('client/src/js/oembeddable.js', 'js');

mix.webpackConfig({
    externals: {
        // Externals will not be compiled-in (eg import $ from 'jQuery', combined with external 'jquery': 'jQuery' means jQuery gets provided externally)
        // For external modules provided by SilverStripe see: https://github.com/silverstripe/webpack-config/blob/master/js/externals.js
        i18n: 'i18n',
        jquery: 'jQuery',
        'lib/TinyMCEActionRegistrar': 'TinyMCEActionRegistrar',
        react: 'React',
        'react-dom': 'ReactDom',
        'lib/Injector': 'Injector',
    }
});
