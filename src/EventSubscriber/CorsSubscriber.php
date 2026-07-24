<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * CORS für die öffentliche, lesende API unter /api. Wird u. a. von den statischen
 * Schwester-Apps (uvindex.jetzt usw.) direkt aus dem Browser abgerufen; ohne
 * Access-Control-Allow-Origin blockt der Browser den Cross-Origin-Fetch ("Daten nicht
 * verfügbar"). Es werden ausschließlich öffentliche Messwerte ohne Cookies/Credentials
 * ausgeliefert, daher ist ein offener Origin ("*") unbedenklich und am robustesten.
 *
 * Bewusst als schlanker Subscriber statt nelmio/cors-bundle: nelmio 2.x ist nicht mit
 * Symfony 8 kompatibel (framework-bundle < 8.0).
 */
class CorsSubscriber implements EventSubscriberInterface
{
    private const string API_PREFIX = '/api';

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            // Vor dem Router: OPTIONS-Preflight direkt beantworten (sonst 404 im Router).
            KernelEvents::REQUEST => ['onRequest', 8],
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getMethod() === 'OPTIONS' && $this->isApiPath($request->getPathInfo())) {
            $response = new Response('', Response::HTTP_NO_CONTENT);
            $this->addCorsHeaders($response);
            $event->setResponse($response);
        }
    }

    public function onResponse(ResponseEvent $event): void
    {
        if ($this->isApiPath($event->getRequest()->getPathInfo())) {
            $this->addCorsHeaders($event->getResponse());
        }
    }

    private function isApiPath(string $pathInfo): bool
    {
        return $pathInfo === self::API_PREFIX || str_starts_with($pathInfo, self::API_PREFIX.'/');
    }

    private function addCorsHeaders(Response $response): void
    {
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $response->headers->set('Access-Control-Max-Age', '86400');
    }
}
