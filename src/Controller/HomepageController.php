<?php

namespace App\Controller;

use App\Service\UpdateChecker;
use App\Service\DatabaseUpdater;
use App\Repository\ForecastRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(
        ForecastRepository $forecastRepository,
        UpdateChecker $updateChecker,
        DatabaseUpdater $databaseUpdater,
    ): Response
    {
        if(!$updateChecker->isForecastWithin3Hours()){
            $databaseUpdater->fetchWeatherForecasts();
        }
        
        $forecastsWithLocations = $forecastRepository->findFirstForecastPerCity();


        return $this->render('homepage/index.html.twig', [
            'forecastsWithLocations' => $forecastsWithLocations,

         ]);
    }
}
