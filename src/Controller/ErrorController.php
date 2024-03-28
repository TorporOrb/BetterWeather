<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ErrorController extends AbstractController
{
    #[Route('/error/{_locale}', 
    name: 'app_error',
    requirements: ['_locale' => 'en|de|nl'], 
    defaults: ['_locale' => null, 'message' => 'An error occurred.', 'title' => 'Error'])]
    public function index(): Response
    {
        return $this->render('error/index.html.twig');
    }
}
