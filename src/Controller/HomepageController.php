<?php

namespace App\Controller;

use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(
        ForecastRepository $forecastRepository,
        LocationRepository $locationRepository,
    ): Response
    {
        $forecasts = $forecastRepository->findFirstForecastPerCity();

        return $this->render('homepage/index.html.twig', [
            'forecasts' => $forecasts,

         ]);
    }
}
