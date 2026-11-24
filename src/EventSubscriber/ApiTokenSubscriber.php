<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Schützt die schreibenden API-Endpunkte (POST/PUT/PATCH/DELETE unterhalb von
 * /api) mit einem gemeinsamen Bearer-Token aus der Umgebungsvariable
 * LUFT_API_TOKEN.
 *
 * Opt-in: Ist LUFT_API_TOKEN leer/nicht gesetzt, bleibt das Verhalten
 * unverändert (kein Schutz). Dadurch erhalten die Provider nicht unvermittelt
 * 401, bevor sie das Token mitsenden. Sobald das Token gesetzt ist, werden
 * schreibende Requests ohne gültigen Header mit 401 abgewiesen. Lesende
 * Requests (GET, u. a. /api/doc) bleiben immer öffentlich.
 */
class ApiTokenSubscriber implements EventSubscriberInterface
{
    private const MUTATING_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function __construct(private readonly string $apiToken)
    {
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 16],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ('' === $this->apiToken) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->requiresAuthentication($request)) {
            return;
        }

        $providedToken = $this->extractBearerToken($request);

        if (null === $providedToken || !hash_equals($this->apiToken, $providedToken)) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid or missing API token.');
        }
    }

    private function requiresAuthentication(Request $request): bool
    {
        return in_array($request->getMethod(), self::MUTATING_METHODS, true)
            && str_starts_with($request->getPathInfo(), '/api');
    }

    private function extractBearerToken(Request $request): ?string
    {
        $authorizationHeader = $request->headers->get('Authorization', '');

        if (!str_starts_with($authorizationHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authorizationHeader, 7);

        return '' !== $token ? $token : null;
    }
}
