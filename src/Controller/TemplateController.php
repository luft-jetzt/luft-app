<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends AbstractController
{
    public function cityListAction(): Response
    {
        return $this->render(
            'Template/city_list.html.twig', [
                'cityList' => $this->getDoctrine()->getRepository(City::class)->findBy([], ['name' => 'ASC']),
            ]
        );
    }
}
