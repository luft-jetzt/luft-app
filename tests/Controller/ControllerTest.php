<?php declare(strict_types=1);

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerTest extends WebTestCase
{
    public function testFrontpage(): void
    {
        $client = $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testImpressPage(): void
    {
        $client = $client = static::createClient();

        $client->request('GET', '/impress');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertSelectorTextContains('html h2', 'Datenschutzerklärung');
    }

    public function testPrivacyPage(): void
    {
        $client = $client = static::createClient();

        $client->request('GET', '/privacy');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertSelectorTextContains('html h2', 'Datenschutzerklärung');
    }
}
