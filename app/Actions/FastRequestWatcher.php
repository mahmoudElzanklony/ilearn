<?php

namespace App\Actions;

use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\View\View;
use Laravel\Telescope\Watchers\RequestWatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class FastRequestWatcher  extends RequestWatcher
{
    /**
     * Format the given response object.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return array|string
     */
    protected function response(Response $response)
    {

        if ($response instanceof RedirectResponse) {
            return 'Redirected to '.$response->getTargetUrl();
        }

        if ($response instanceof IlluminateResponse && $response->getOriginalContent() instanceof View) {
            return [
                'view' => $response->getOriginalContent()->getPath(),
                'data' => $this->extractDataFromView($response->getOriginalContent()),
            ];
        }



        return 'HTML Response';

    }
}
