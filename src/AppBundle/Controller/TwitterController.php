<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\Photo;
use AppBundle\PermalinkManager\SqibePermalinkManager;
use Codebird\Codebird;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class TwitterController extends AbstractController
{
    public function postAction(Request $request): Response
    {
        $photoId = $request->query->get('photoId');

        $city = $this->getCity($request);

        /** @var Photo $photo */
        $photo = $this->getDoctrine()->getRepository(Photo::class)->find($photoId);

        $cb = $this->getCodeBird();
        $cb->setToken($city->getTwitterToken(), $city->getTwitterSecret());

        $message = sprintf('%s: %s', $photo->getTitle(), $this->getPermalink($photo));
        $status = sprintf('status=%s', $message);

        $reply = $cb->statuses_update($status);

        return new Response($reply);
    }

    protected function getCodeBird(): Codebird
    {
        Codebird::setConsumerKey($this->getParameter('twitter.client_id'), $this->getParameter('twitter.client_secret'));

        return Codebird::getInstance();
    }

    protected function getPermalink(Photo $photo): string
    {
        if (!$photo->getPermalink()) {
            /** @var SqibePermalinkManager $permalinkManager */
            $permalinkManager = $this->get('app.permalink_manager');

            $permalinkManager->createPermalink($photo);

            $this->getDoctrine()->getManager()->flush();
        }

        return $photo->getPermalink();
    }
}
