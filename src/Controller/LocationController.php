<?php

namespace App\Controller;

use DateTime;
use App\Entity\Location;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/location')]
class LocationController extends AbstractController
{
    #[Route('/{city}', name: 'app_location')]
    public function index(
        LocationRepository $locationRepository,
        ForecastRepository $forecastRepository,
        string $city
        ): Response
    {
        $location = $locationRepository->findOneBy(([
            'city_name' => $city,
        ]));

        if(!$location){
            throw $this->createNotFoundException("Location not found");
        }

        $forecasts = $forecastRepository->findNoonForecastsForLocation($location);
        
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
