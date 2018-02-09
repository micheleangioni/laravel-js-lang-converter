<?php namespace MicheleAngioni\LaravelJsLangConverter;

use Illuminate\Support\ServiceProvider;

class LaravelJsLangConverterServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('laravel_js_lang.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php', 'laravel_js_lang'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('localization.js', function ($app) {
            $files = $app['files'];
            $langs = $app['path.base'] . '/resources/lang';
            $generator = new Generators\LangJsGenerator($files, $langs);

            return new Commands\LangJsCommand($generator);
        });

        $this->commands('localization.js');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['localization.js'];
    }
}
