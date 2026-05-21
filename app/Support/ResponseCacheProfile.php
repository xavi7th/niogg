<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests;

class ResponseCacheProfile extends CacheAllSuccessfulGetRequests
{
    public function shouldCacheRequest(Request $request): bool
    {
        if ($request->is('admin/*') || $request->is('administrative-logs/*') || $request->is('application-logs/*')) {
            return FALSE;
        }

        if ($request->getMethod() !== 'GET') {
            return FALSE;
        }

        return parent::shouldCacheRequest($request);
    }

    public function shouldCacheResponse(Response $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return FALSE;
        }

        if (session()->has('flash')) {
            return FALSE;
        }

        return parent::shouldCacheResponse($response);
    }
}
