<?php

use Controller\StravaController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['strava.controller'] = function() use ($app) {
    return new StravaController($app['twig'], $app['strava.api'], $app['session'], $app['url_generator']);
};
$app->get('/strava/authentication', "strava.controller:authenticationAction")->bind('authentication');
$app->get('/strava/logout', "strava.controller:logoutAction")->bind('logout');
$app->get('/strava/index', "strava.controller:indexAction")->bind('default');
$app->get('/strava/activities', "strava.controller:activitesAction")->bind('activites');

$app->get('/', function () use ($app) {
    return new RedirectResponse('/strava/index');
})
->bind('homepage')
;

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
