<?php

namespace StravaAccess;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AccessBehavior
 */
class AccessBehavior
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Application
     */
    protected $app;

    /**
     * AccessBehavior constructor.
     * @param Request     $request
     * @param Application $app
     */
    public function __construct(Request $request, Application $app)
    {
        $this->request = $request;
        $this->app = $app;
    }

    /**
     * @return RedirectResponse
     */
    public function authenticate()
    {
        if (null === $this->request->get('code') && null === $this->app['session']->get('strava_acces_token')) {
            $url = $this->app['strava.api']->authenticationUrl(
                $this->app['url_generator']->generate(
                    'authentication',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            );

            return new RedirectResponse($url);
        } else {
            $this->app['strava.api']->setAccessToken($this->app['session']->get('strava_acces_token'));
        }
    }
}