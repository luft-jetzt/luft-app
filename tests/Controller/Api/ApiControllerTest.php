<?php declare(strict_types=1);

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testApiDocVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/doc');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
