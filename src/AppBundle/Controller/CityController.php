<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CityController extends AbstractController
{
    public function showAction(Request $request, string $citySlug): Response
    {
        return new Response($citySlug);
    }
}
