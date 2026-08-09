<?php declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ApiTokenSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ApiTokenSubscriberTest extends TestCase
{
    private const TOKEN = 's3cr3t-token';

    public function testWriteRequestIsAllowedWhenTokenIsNotConfigured(): void
    {
        $subscriber = new ApiTokenSubscriber('');
        $event = $this->createRequestEvent('PUT', '/api/value');

        $subscriber->onRequest($event);

        $this->expectNotToPerformAssertions();
    }

    public function testWriteRequestWithoutAuthorizationHeaderIsRejected(): void
    {
        $subscriber = new ApiTokenSubscriber(self::TOKEN);
        $event = $this->createRequestEvent('PUT', '/api/value');

        $this->expectException(UnauthorizedHttpException::class);

        $subscriber->onRequest($event);
    }

    public function testWriteRequestWithWrongTokenIsRejected(): void
    {
        $subscriber = new ApiTokenSubscriber(self::TOKEN);
        $event = $this->createRequestEvent('PUT', '/api/value', 'Bearer wrong-token');

        $this->expectException(UnauthorizedHttpException::class);

        $subscriber->onRequest($event);
    }

    public function testWriteRequestWithCorrectTokenIsAllowed(): void
    {
        $subscriber = new ApiTokenSubscriber(self::TOKEN);
        $event = $this->createRequestEvent('PUT', '/api/value', 'Bearer '.self::TOKEN);

        $subscriber->onRequest($event);

        $this->expectNotToPerformAssertions();
    }

    public function testReadRequestStaysPublicEvenWithTokenConfigured(): void
    {
        $subscriber = new ApiTokenSubscriber(self::TOKEN);
        $event = $this->createRequestEvent('GET', '/api/station');

        $subscriber->onRequest($event);

        $this->expectNotToPerformAssertions();
    }

    public function testNonApiWriteRequestIsNotGuarded(): void
    {
        $subscriber = new ApiTokenSubscriber(self::TOKEN);
        $event = $this->createRequestEvent('POST', '/search/query');

        $subscriber->onRequest($event);

        $this->expectNotToPerformAssertions();
    }

    public function testSubRequestIsIgnored(): void
    {
        $subscriber = new ApiTokenSubscriber(self::TOKEN);
        $event = $this->createRequestEvent('PUT', '/api/value', null, HttpKernelInterface::SUB_REQUEST);

        $subscriber->onRequest($event);

        $this->expectNotToPerformAssertions();
    }

    private function createRequestEvent(
        string $method,
        string $path,
        ?string $authorizationHeader = null,
        int $requestType = HttpKernelInterface::MAIN_REQUEST,
    ): RequestEvent {
        $request = Request::create($path, $method);

        if (null !== $authorizationHeader) {
            $request->headers->set('Authorization', $authorizationHeader);
        }

        return new RequestEvent($this->createStub(HttpKernelInterface::class), $request, $requestType);
    }
}
