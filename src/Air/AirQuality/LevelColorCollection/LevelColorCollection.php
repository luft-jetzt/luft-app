<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorCollection;

use App\Air\AirQuality\LevelColors\LevelColorsInterface;
use App\Air\AirQuality\LevelColors\StandardLevelColors;
use App\Util\ClassUtil;
use Symfony\Component\String\ByteString;
use function Symfony\Component\String\b;

class LevelColorCollection implements LevelColorCollectionInterface
{
    protected array $levelColorsList = [];

    public function addLevelColors(LevelColorsInterface $levelColors): LevelColorCollectionInterface
    {
        $this->levelColorsList[$levelColors->getIdentifier()] = $levelColors;

        return $this;
    }

    #[\Override]
    public function getLevelColorsList(): array
    {
        return $this->levelColorsList;
    }

    #[\Override]
    public function getLevelColorsForMeasurement(string $measurementIdentifier): LevelColorsInterface
    {
        if (!array_key_exists($measurementIdentifier, $this->levelColorsList)) {
            return new StandardLevelColors();
        }

        return $this->levelColorsList[$measurementIdentifier];
    }
}
