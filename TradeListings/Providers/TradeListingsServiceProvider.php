<?php

namespace Extensions\TradeListings\Providers;

use Esemve\Hook\Facades\Hook;
use Extensions\TradeListings\Console\MigrateTradeListingComments;
use Extensions\TradeListings\Models\TradeListing;
use Illuminate\Support\ServiceProvider;

class TradeListingsServiceProvider extends ServiceProvider {
    /**
     * @var string
     */
    protected $moduleName = 'TradeListings';

    /**
     * @var string
     */
    protected $moduleNameLower = 'trade_listings';

    /**
     * Boot the application events.
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->handleHooks();
        $this->registerCommands();
    }

    /**
     * Register the service provider.
     */
    public function register() {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register views.
     */
    public function registerViews() {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     */
    public function registerTranslations() {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

    /**
     * Register config.
     */
    protected function registerConfig() {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower.'.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Make various changes via hooks.
     */
    private function handleHooks() {
        // Multiple listeners can impact the same hook, but only so long as
        // they have different priorities set. Since broadly extensions should
        // strive to be mutually compatible where feasible regardless, the name
        // as converted to an integer is used here so as to provide a unique number.
        $priority = substr(base_convert(md5($this->moduleNameLower), 16, 10), -5);

        Hook::listen('commands_add_site_settings', function ($callback, $output, $settings) {
            if (empty($output)) {
                $output = $settings;
            }
            $output['trade_listing_duration'] = [
                'value'       => 14,
                'description' => 'Number of days a trade listing is displayed for.',
            ];

            return $output;
        }, $priority);

        Hook::listen('template.home_activity_sidebar', function ($callback, $output, $data) {
            return $output.view('trade_listings::widgets._sidebar_row');
        }, $priority);

        Hook::listen('controllers_comment_model_type', function ($callback, $output, $settings) {
            if (empty($output)) {
                $output = $settings;
            }
            $output['Extensions\TradeListings\Models\TradeListing'] = [
                'model' => TradeListing::class,
                'post'  => 'your trade listing',
            ];

            return $output;
        }, $priority);

        // Proof-of-Terms Link
        Hook::listen('controllers_trades_post_create', function ($callback, $output, $fields) {
            if (empty($output)) {
                $output = $fields;
            }
            $output[] = 'terms_link';

            return $output;
        }, $priority);

        Hook::listen('managers_trades_create', function ($callback, $output, $data) {
            if (empty($output)) {
                $output = [];
            }
            $output['terms_link'] = $data['terms_link'] ?? null;

            return $output;
        }, $priority);

        Hook::listen('template.home_trades_create', function ($callback, $output, $data) {
            return $output.view('trade_listings::trades._terms_link_field');
        }, $priority);

        Hook::listen('template.home_trades_view_widget', function ($callback, $output, $data) {
            return $output.view('trade_listings::trades._terms_link_display', [
                'trade' => $data['trade'],
            ]);
        }, $priority);

        Hook::listen('template.home_trades_view_status', function ($callback, $output, $data) {
            return $output.view('trade_listings::trades._terms_link_display', [
                'trade' => $data['trade'],
            ]);
        }, $priority);
    }

    /**
     * Register any artisan commands.
     */
    private function registerCommands() {
        $this->commands([
            //
            MigrateTradeListingComments::class,
        ]);
    }

    private function getPublishableViewPaths(): array {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
}
