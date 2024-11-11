<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    protected function authorization()
    {
        $this->gate();

       /* if(!(request()->filled('secret') && request('secret') == 'ilearn2024!!@@!!')){
            abort(403);
        }*/

        Telescope::auth(function ($request) {
            return app()->environment('local') || app()->environment('production') ||
                Gate::check('viewTelescope', [$request->user()]);
        });
    }

    public function register(): void
    {
        // Telescope::night();

         $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('production');
        //dd($this->app, $this->app->environment('local') , $this->app->environment('production'));

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        if ($this->app->environment('production')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return request()->filled('secret') && request('secret') == 'ilearn2024!!@@!!';
        });
    }
}
