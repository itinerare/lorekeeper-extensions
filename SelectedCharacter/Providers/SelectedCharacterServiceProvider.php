<?php

namespace Extensions\SelectedCharacter\Providers;

use Esemve\Hook\Facades\Hook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class SelectedCharacterServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'SelectedCharacter';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'selectedcharacter';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->handleHooks();
        $this->registerCommands();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

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
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
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

        //
        Hook::listen('managers_character_move_character', function($callback, $output, $character) {
            if (empty($output)) {
                $output = $character;
            }
            if($output->user && $output->user->settings->selected_character_id == $output->id) {
                $output->user->settings->selected_character_id = null;
                $output->user->settings->save();
            }
            return $output;
        }, $priority);

        Hook::listen('template.home_characters_addition', function($callback, $output, $data) {
            return $output.view('selectedcharacter::_select_character', [
                'characters' => $data['characters'],
            ]);
        }, $priority);

        Hook::listen('template.users_profile_content_characters', function($callback, $output, $data) {
            if(config('selectedcharacter.remove_profile_characters')) {
                return '';
            } else {
                return $output;
            }
        }, $priority);

        Hook::listen('template.users_profile_content_assets', function($callback, $output, $data) {
            return view('selectedcharacter::_user_profile_assets', [
                'user' => $data['user'],
                'items' => $data['items'],
            ]);
        }, $priority);
    }

    /**
     * Register any artisan commands.
     */
    private function registerCommands() {
        $this->commands([
            //
        ]);
    }
}
