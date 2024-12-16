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

        $content = $response->getContent();

        if (is_string($content) &&
            is_array(json_decode($content, true)) &&
            json_last_error() === JSON_ERROR_NONE) {
            return $this->contentWithinLimits($content)
                ? json_decode($response->getContent(), true) : 'Purged by Telescope';
        }

        return "HTML Response";

    }
}
