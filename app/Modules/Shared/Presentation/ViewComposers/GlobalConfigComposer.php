<?php

namespace App\Modules\Shared\Presentation\ViewComposers;

use App\Modules\Shared\Domain\Services\ConfigurationService;
use Illuminate\View\View;

/**
 * Shares global configuration values to ALL Blade views.
 * Registered in ModuleServiceProvider::boot() for view('*').
 *
 * Available in every view:
 *   $appName        — value of 'app_name' config key
 *   $appDescription — value of 'app_description' config key
 *   $appConfig      — full ['variable' => 'value'] array for custom keys
 */
class GlobalConfigComposer
{
    public function __construct(
        private ConfigurationService $config
    ) {}

    public function compose(View $view): void
    {
        $view->with([
            'appName'        => $this->config->get('app_name', config('app.name')),
            'appDescription' => $this->config->get('app_description', ''),
            'appConfig'      => $this->config->all(),
        ]);
    }
}
