<?php declare(strict_types=1);

namespace App\Tests\Air\ValueDataConverter;

use App\Air\Pollutant\PollutantInterface;
use App\Air\ValueDataConverter\ValueDataConverter;
use PHPUnit\Framework\TestCase;

class ValueDataConverterTest extends TestCase
{
    private function convertPollutant(string $identifier): ?int
    {
        $method = new \ReflectionMethod(ValueDataConverter::class, 'convertPollutant');

        return $method->invoke(null, $identifier);
    }

    public function testKnownIdentifierIsMapped(): void
    {
        $this->assertSame(PollutantInterface::POLLUTANT_PM10, $this->convertPollutant('pm10'));
    }

    public function testIdentifierIsCaseInsensitive(): void
    {
        $this->assertSame(PollutantInterface::POLLUTANT_PM10, $this->convertPollutant('PM10'));
    }

    public function testUnderscoresAreStripped(): void
    {
        $this->assertSame(PollutantInterface::POLLUTANT_UVINDEXMAX, $this->convertPollutant('UV_INDEX_MAX'));
    }

    public function testUnknownIdentifierReturnsNull(): void
    {
        $this->assertNull($this->convertPollutant('does_not_exist'));
    }
}
