<?php

namespace App\Controller;

use DateTime;
use App\Entity\Location;
use App\Service\UpdateChecker;
use App\Service\DatabaseUpdater;
use App\Service\ValidationService;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/location')]
class LocationController extends AbstractController
{
    #[Route('/{city}/{_locale}', name: 'app_location',
    requirements: ['_locale' => 'en|de|nl'], 
    defaults: ['_locale' => null])]
    public function index(
        LocationRepository $locationRepository,
        ForecastRepository $forecastRepository,
        UpdateChecker $updateChecker,
        DatabaseUpdater $databaseUpdater,
        ValidationService $validationService,
        string $city
        ): Response
    {
        if (!$validationService->isValidCity($city)){
            return $this->redirectToRoute('app_error');
        }
        
        if(!$updateChecker->isForecastWithin3Hours()){
            $databaseUpdater->fetchWeatherForecasts();
        }

        $location = $locationRepository->findOneBy(([
            'city_name' => $city,
        ]));

        if(!$location){
            throw $this->createNotFoundException("Location not found");
        }

        $forecasts = $forecastRepository->findWeeklyForecastsForLocation($location);
        
        $clusteredForecasts = [];
        foreach($forecasts as $forecast){
            $date = $forecast->getDate()->format('Y-m-d');
            $clusteredForecasts[$date][] = $forecast;
        }

        return $this->render('location/index.html.twig', [
            'location' => $location,
            'clusteredForecasts' => $clusteredForecasts,
        ]);
    }
}
