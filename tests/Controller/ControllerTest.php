<?php declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerTest extends WebTestCase
{
    public function testFrontpage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testImpressPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/impress');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', 'Impressum');
    }

    public function testPrivacyPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/privacy');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', 'Datenschutzerklärung');
    }
}
