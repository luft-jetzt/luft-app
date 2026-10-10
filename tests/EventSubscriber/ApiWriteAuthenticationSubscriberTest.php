<?php declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ApiWriteAuthenticationSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiWriteAuthenticationSubscriberTest extends TestCase
{
    private const TOKEN = 'super-secret-token';

    public function testSubscribesToRequestEvent(): void
    {
        $this->assertArrayHasKey(KernelEvents::REQUEST, ApiWriteAuthenticationSubscriber::getSubscribedEvents());
    }

    public function testReadRequestIsNotChallenged(): void
    {
        $event = $this->dispatch('GET', '/api/station', null);

        $this->assertFalse($event->hasResponse());
    }

    public function testNonApiWriteRequestIsNotChallenged(): void
    {
        $event = $this->dispatch('POST', '/contact', null);

        $this->assertFalse($event->hasResponse());
    }

    public function testApiDocGetStaysPublic(): void
    {
        $event = $this->dispatch('GET', '/api/doc', null);

        $this->assertFalse($event->hasResponse());
    }

    public function testWriteWithValidTokenPasses(): void
    {
        $event = $this->dispatch('PUT', '/api/station', 'Bearer ' . self::TOKEN);

        $this->assertFalse($event->hasResponse());
    }

    public function testWriteWithoutTokenIsUnauthorized(): void
    {
        $this->expectException(UnauthorizedHttpException::class);

        $this->dispatch('PUT', '/api/station', null);
    }

    public function testWriteWithInvalidTokenIsDenied(): void
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->dispatch('POST', '/api/DENI200', 'Bearer wrong-token');
    }

    public function testWriteWithNonBearerHeaderIsUnauthorized(): void
    {
        $this->expectException(UnauthorizedHttpException::class);

        $this->dispatch('PUT', '/api/value', 'Basic ' . base64_encode('a:b'));
    }

    public function testUnconfiguredTokenRejectsEveryWrite(): void
    {
        $this->expectException(AccessDeniedHttpException::class);

        $subscriber = new ApiWriteAuthenticationSubscriber('');
        $subscriber->onRequest($this->createEvent('PUT', '/api/station', 'Bearer ' . self::TOKEN));
    }

    public function testSubRequestIsIgnored(): void
    {
        $subscriber = new ApiWriteAuthenticationSubscriber(self::TOKEN);
        $request = Request::create('/api/station', 'PUT');
        $event = new RequestEvent($this->createStub(HttpKernelInterface::class), $request, HttpKernelInterface::SUB_REQUEST);

        $subscriber->onRequest($event);

        $this->assertFalse($event->hasResponse());
    }

    private function dispatch(string $method, string $path, ?string $authorization): RequestEvent
    {
        $subscriber = new ApiWriteAuthenticationSubscriber(self::TOKEN);
        $event = $this->createEvent($method, $path, $authorization);

        $subscriber->onRequest($event);

        return $event;
    }

    private function createEvent(string $method, string $path, ?string $authorization): RequestEvent
    {
        $request = Request::create($path, $method);

        if (null !== $authorization) {
            $request->headers->set('Authorization', $authorization);
        }

        return new RequestEvent($this->createStub(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);
    }
}
