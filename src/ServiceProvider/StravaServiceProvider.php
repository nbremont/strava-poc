<?php

namespace ServiceProvider;

use Iamstuartwilson\StravaApi;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class StravaServiceProvider
 */
class StravaServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        if (!isset($app['strava.client_id'])) {
            $app['strava.client_id'] = '';
        }
        if (!isset($app['strava.client_secret'])) {
            $app['strava.client_secret'] = '';
        }

        $app['strava.api'] = function() use ($app) {
            return new StravaApi($app['strava.client_id'], $app['strava.client_secret']);
        };
    }
}