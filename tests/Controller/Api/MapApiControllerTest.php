<?php declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Controller\Api\MapApiController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MapApiControllerTest extends WebTestCase
{
    public function testUnknownPollutantIsRejected(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/map/stations.geojson?pollutant=plutonium');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('error', json_decode($client->getResponse()->getContent(), true));
    }

    public function testUnknownScopeIsRejected(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/map/stations.geojson?scope=everything');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testScopeAllRequiresBbox(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/map/stations.geojson?scope=all');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('bbox', json_decode($client->getResponse()->getContent(), true)['error']);
    }

    public function testMalformedBboxIsRejected(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/map/stations.geojson?scope=all&bbox=9.5,53.2,10.5');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testNonNumericBboxIsRejected(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/map/stations.geojson?scope=all&bbox=a,b,c,d');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testBboxOutsideGermanyIsRejected(): void
    {
        $client = static::createClient();

        // liegt komplett westlich des Deutschland-Umgriffs, nach dem Klemmen bleibt kein Ausschnitt übrig
        $client->request('GET', '/api/map/stations.geojson?scope=all&bbox=-30,-30,-20,-20');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testBboxIsClampedToGermany(): void
    {
        $controller = (new \ReflectionClass(MapApiController::class))->newInstanceWithoutConstructor();

        $method = new \ReflectionMethod(MapApiController::class, 'parseBbox');

        $this->assertEquals([5.0, 47.0, 16.0, 56.0], $method->invoke($controller, '-10,40,20,60'));
        $this->assertEquals([9.5, 53.2, 10.5, 53.8], $method->invoke($controller, '9.5,53.2,10.5,53.8'));
        $this->assertNull($method->invoke($controller, '10.5,53.2,9.5,53.8')); // west > ost
        $this->assertNull($method->invoke($controller, '9.5,53.8,10.5,53.2')); // süd > nord
    }
}
