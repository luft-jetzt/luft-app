<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends AbstractController
{
    public function cityListAction(Request $request): Response
    {
        return $this->render(
            'AppBundle:Template:city_list.html.twig', [
                'cityList' => $this->getDoctrine()->getRepository(City::class)->findBy([], ['name' => 'ASC']),
            ]
        );
    }
}
