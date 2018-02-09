<?php

namespace Caldera\StaticmapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CalderaStaticmapBundle:Default:index.html.twig');
    }
}
