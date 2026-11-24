<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff', false);
        $headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);

        if ($event->getRequest()->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains', false);
        }

        $this->addContentSecurityPolicy($response);
    }

    /*
     * Frame-ancestors is the CSP equivalent of X-Frame-Options and is safe to
     * enforce without inventorying every external asset host. A full script/style
     * CSP is intentionally left out here because it requires a frontend asset audit
     * and runtime verification; see the accompanying pull request for details.
     */
    protected function addContentSecurityPolicy(Response $response): void
    {
        $response->headers->set("Content-Security-Policy", "frame-ancestors 'self'", false);
    }
}
