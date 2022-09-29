<?php

namespace Extensions\Wishlists\Providers;

use Esemve\Hook\Facades\Hook;
use Illuminate\Support\ServiceProvider;

class WishlistsServiceProvider extends ServiceProvider {
    /**
     * @var string
     */
    protected $moduleName = 'Wishlists';

    /**
     * @var string
     */
    protected $moduleNameLower = 'wishlists';

    /**
     * Boot the application events.
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->handleHooks();
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
     * Make various changes via hooks.
     */
    public function handleHooks() {
        Hook::listen('template.home_activity_sidebar', function ($callback, $output, $data) {
            return $output."\n".view('wishlists::home._sidebar_row');
        });

        Hook::listen('template.home_inventory_stack_name', function ($callback, $output, $data) {
            return $output.view('wishlists::_wishlist_add', [
                'small' => true,
                'item'  => $data['item'],
            ]);
        });

        Hook::listen('template.user_user_sidebar', function ($callback, $output, $data) {
            return $output."\n".view('wishlists::user._sidebar_row', [
                'user' => $data['user'],
            ]);
        });

        Hook::listen('template.world_item_entry_title', function ($callback, $output, $data) {
            return $output.view('wishlists::_wishlist_add', [
                'class' => 'float-right mx-2',
                'item'  => $data['item'],
            ]);
        });

        Hook::listen('template.world_item_page_name', function ($callback, $output, $data) {
            return $output.view('wishlists::_wishlist_add', [
                'class' => 'float-right',
                'item'  => $data['item'],
            ]);
        });

        Hook::listen('template.shops_shop_item_name', function ($callback, $output, $data) {
            return $output.view('wishlists::_wishlist_check', [
                'item' => $data['item'],
            ]);
        });

        Hook::listen('template.shops_stock_item_name', function ($callback, $output, $data) {
            return $output.view('wishlists::_wishlist_add', [
                'small' => true,
                'item'  => $data['stock']->item,
            ]);
        });

        Hook::listen('template.character_inventory_stack_name', function ($callback, $output, $data) {
            return $output.view('wishlists::_wishlist_add', [
                'small' => true,
                'item'  => $data['item'],
            ]);
        });
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
