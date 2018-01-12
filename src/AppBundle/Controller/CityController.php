<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\User;
use Codebird\Codebird;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CityController extends AbstractController
{
    public function showAction(Request $request, string $citySlug): Response
    {
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        $stationList = $this->getStationListForCity($city);
        $stationsBoxList = $this->createBoxListForStationList($stationList);

        return $this->render('AppBundle:City:show.html.twig', [
            'city' => $city,
            'stationList' => $stationList,
            'stationBoxList' => $stationsBoxList,
        ]);
    }

    public function twitterAction(Request $request, string $citySlug): Response
    {
        /** @var City $city */
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city || $city->getUser()) {
            throw $this->createNotFoundException();
        }

        $this->getSession()->set('twitterCity', $city);

        return $this->render('AppBundle:City:twitter.html.twig', [
            'city' => $city,
        ]);
    }

    protected function getSession(): Session
    {
        /** @var Session $session */
        $session = $this->get('session');

        return $session;
    }

    public function twitterAuthAction(Request $request, string $citySlug): Response
    {
        /** @var City $city */
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        $session = $this->getSession();

        $cb = $this->getCodeBird();

        $callbackUrl = $this->generateUrl('twitter_token', ['citySlug' => $city->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);

        $reply = $cb->oauth_requestToken([
            'oauth_callback' => $callbackUrl,
        ]);

        $cb->setToken($reply->oauth_token, $reply->oauth_token_secret);
        $session->set('oauth_token', $reply->oauth_token);
        $session->set('oauth_token_secret', $reply->oauth_token_secret);
        $session->set('oauth_verify', true);

        return new RedirectResponse($cb->oauth_authorize());
    }

    public function twitterTokenAction(Request $request, UserInterface $user, string $citySlug): Response
    {
        /** @var City $city */
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        $session = $this->getSession();

        $cb = $this->getCodeBird();
        $cb->setToken($session->get('oauth_token'), $session->get('oauth_token_secret'));

        $verifier = $request->query->get('oauth_verifier');

        if ($verifier && $session->has('oauth_verify')) {
            $session->remove('oauth_verify');

            $reply = $cb->oauth_accessToken([
                'oauth_verifier' => $verifier
            ]);

            $this->saveCityAccess($request, $user, $reply);
        }

        $showUrl = $this->generateUrl('show_city', ['citySlug' => $city->getSlug()]);

        return new RedirectResponse($showUrl);
    }

    protected function getCodeBird(): Codebird
    {
        Codebird::setConsumerKey($this->getParameter('twitter.client_id'), $this->getParameter('twitter.client_secret'));

        return Codebird::getInstance();
    }


    protected function saveCityAccess(Request $request, User $user, \stdClass $reply): User
    {
        $user->setTwitterSecret($reply->oauth_token_secret);

        $this->getDoctrine()->getManager()->flush();

        return $user;
    }
}
