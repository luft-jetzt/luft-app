<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Pollution\Value\Value;
use Elastica\Document;
use Elastica\Index;

class ElasticPersister implements PersisterInterface
{
    protected Index $index;

    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    public function persistValues(array $values): PersisterInterface
    {
        $documentList = [];

        /** @var Value $value */
        foreach ($values as $value) {
            $document = new Document();
            $document->setData([
                'value' => $value->getValue(),
                'pollutant' => (int) $value->getPollutant(),
                'dateTime' => $value->getDateTime()->format('Y-m-d H:i:s'),
                'station' => ['stationCode' => $value->getStation()],
            ])
                ->setType('data');

            $documentList[] = $document;
        }

        $this->index->addDocuments($documentList);

        return $this;
    }

    public function getNewValueList(): array
    {
        return [];
    }

    public function reset(): PersisterInterface
    {
        return $this;
    }
}
