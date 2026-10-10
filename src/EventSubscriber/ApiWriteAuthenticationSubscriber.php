<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Enforces a shared bearer token on all writing (POST/PUT/PATCH/DELETE)
 * requests below the /api path.
 *
 * The token is provided through the API_WRITE_TOKEN environment variable and
 * must be sent as an "Authorization: Bearer <token>" header. Read requests
 * (GET/HEAD) as well as everything outside /api are left untouched.
 *
 * Fails closed: if no token is configured, every write request is rejected.
 */
class ApiWriteAuthenticationSubscriber implements EventSubscriberInterface
{
    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function __construct(protected readonly string $apiWriteToken)
    {
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        // Run before the controller (and after routing) resolves the request.
        return [
            KernelEvents::REQUEST => ['onRequest', 8],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->isApiWriteRequest($request)) {
            return;
        }

        if ('' === $this->apiWriteToken) {
            throw new AccessDeniedHttpException('API write access is not configured.');
        }

        $providedToken = $this->extractBearerToken($request);

        if (null === $providedToken) {
            throw new UnauthorizedHttpException('Bearer', 'Missing API token.');
        }

        if (!hash_equals($this->apiWriteToken, $providedToken)) {
            throw new AccessDeniedHttpException('Invalid API token.');
        }
    }

    private function isApiWriteRequest(Request $request): bool
    {
        $path = $request->getPathInfo();

        if ('/api' !== $path && !str_starts_with($path, '/api/')) {
            return false;
        }

        return in_array($request->getMethod(), self::WRITE_METHODS, true);
    }

    private function extractBearerToken(Request $request): ?string
    {
        $header = $request->headers->get('Authorization');

        if (null === $header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($header, 7));

        return '' === $token ? null : $token;
    }
}
