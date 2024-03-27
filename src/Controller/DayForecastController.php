<?php

namespace App\Controller;

use DateTime;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
use App\Service\DatabaseUpdater;
use App\Service\UpdateChecker;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/location')]
class DayForecastController extends AbstractController
{
    #[Route('/{city}/{date}')]
    public function show(
        LocationRepository $locationRepository,
        ForecastRepository $forecastRepository,
        UpdateChecker $updateChecker,
        DatabaseUpdater $databaseUpdater,

        string $city, 
        string $date,

        ): Response
    {
        if(!$updateChecker->isForecastWithin3Hours()){
            $databaseUpdater->fetchWeatherForecasts();
        }

        $location = $locationRepository->findOneBy(([
            'city_name' => $city,
        ]));

        $parsedDate = DateTime::createFromFormat('Y-m-d', $date);

        if(!$location){
            throw $this->createNotFoundException("Location not found");
        }

        $forecasts = $forecastRepository->findForecastsForLocationAndDate($location, $parsedDate);
        
        return $this->render('day_forecast/index.html.twig', [
            'location' => $location,
            'forecasts' => $forecasts,
        ]);
    }
}
