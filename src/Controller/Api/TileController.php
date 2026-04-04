<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Station;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TileController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly CacheInterface $cache,
    ) {
    }

    #[Route('/api/tiles/{z}/{x}/{y}.pbf', name: 'api_tile', requirements: ['z' => '\d+', 'x' => '\d+', 'y' => '\d+'], methods: ['GET'])]
    public function tileAction(int $z, int $x, int $y): Response
    {
        $cacheKey = sprintf('tile_%d_%d_%d', $z, $x, $y);

        $mvt = $this->cache->get($cacheKey, function (ItemInterface $item) use ($z, $x, $y): string {
            $item->expiresAfter(300);

            $tile = $this->managerRegistry
                ->getRepository(Station::class)
                ->findMvtTile($z, $x, $y);

            return $tile ?? '';
        });

        $response = new Response($mvt, Response::HTTP_OK, [
            'Content-Type' => 'application/x-protobuf',
            'Cache-Control' => 'public, max-age=300',
        ]);

        if ($mvt === '') {
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
        }

        return $response;
    }
}
