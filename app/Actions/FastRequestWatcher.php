<?php

namespace App\Actions;

use Laravel\Telescope\Watchers\RequestWatcher;
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

        parent::response($response);

    }
}
