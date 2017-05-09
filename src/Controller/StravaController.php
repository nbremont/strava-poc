<?php

namespace Controller;

use Iamstuartwilson\StravaApi;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Twig_Environment;

/**
 * Class StravaController
 */
class StravaController
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var StravaApi
     */
    protected $stravaApi;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * DefaultController constructor.
     * @param Twig_Environment  $twig
     * @param StravaApi         $stravaApi
     * @param Session           $session
     */
    public function __construct(
        Twig_Environment $twig,
        StravaApi $stravaApi,
        Session $session
    ) {
        $this->twig = $twig;
        $this->stravaApi = $stravaApi;
        $this->session = $session;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function authenticationAction(Request $request)
    {
        if (null === $code = $request->get('code')) {
            throw new BadRequestHttpException('Parameter "code" is missing');
        }

        if (null === $this->session->get('strava_acces_token')) {
            $response = $this->stravaApi->tokenExchange($code);
            $this->session->set('strava_acces_token', $response->access_token);
        }

        return new RedirectResponse(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/strava/index');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function logoutAction(Request $request)
    {
        if (null !== $this->session->get('strava_acces_token')) {
            $this->session->remove('strava_acces_token');
        }

        return new Response('Session has been remove');
    }

    /**
     * @return string
     */
    public function indexAction()
    {
        $activity = $this->stravaApi->get('activities/958460144');

        return new JsonResponse($activity);

        return $this->twig->render('strava/index.html.twig', [
            'activity' => $activity,
        ]);
    }

    /**
     * @return string
     */
    public function activitesAction()
    {
        $activities = $this->stravaApi->get('activities', [
            'after' => (new \DateTime('2016-01-10'))->getTimestamp(),
            'before' => (new \DateTime('2016-01-17'))->getTimestamp(),
        ]);

        return new JsonResponse($activities);

        return $this->twig->render('strava/activities.html.twig', ['activities' => $activities]);
    }
}